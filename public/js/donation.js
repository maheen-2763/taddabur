(() => {
    const byId = (id) => document.getElementById(id);

    // ── Payment method toggle (UPI / Card) ──
    const toggleButtons = document.querySelectorAll(".payment-toggle-btn");
    const panels = document.querySelectorAll(".payment-panel");

    toggleButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            toggleButtons.forEach((b) => b.classList.remove("active"));
            panels.forEach((p) => p.classList.add("d-none"));

            btn.classList.add("active");
            byId(btn.dataset.target)?.classList.remove("d-none");
        });
    });

    // ── Donate ──
    const donateBtn = byId("razorpay-donate-btn");
    if (!donateBtn) return;

    const statusEl = byId("donate-status");
    const MIN_AMOUNT = 1;
    const MAX_AMOUNT = 100000;
    let confirming = false;

    function setStatus(text, type = "info") {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.className = `donate-status mt-3 mb-0 donate-status-${type}`;
    }

    function setBusy(busy) {
        donateBtn.disabled = busy;
        donateBtn.innerHTML = busy
            ? '<span class="spinner-border spinner-border-sm me-2"></span>Please wait...'
            : '<i class="bi bi-credit-card me-1"></i>Donate Now';
    }

    function resetForm() {
        ["donation-amount", "donor-name", "donor-message"].forEach((id) => {
            byId(id).value = "";
        });
        byId("donor-public").checked = false;
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json", // zaroori: warna validation error redirect ban jata hai
                "X-CSRF-TOKEN": window.CONFIG.csrfToken,
            },
            body: JSON.stringify(payload),
        });

        let data = {};
        try {
            data = await response.json();
        } catch (e) {
            /* response JSON nahi tha */
        }

        return { ok: response.ok, status: response.status, data };
    }

    function errorMessage({ status, data }) {
        if (status === 419)
            return "This page has expired. Please refresh and try again.";
        if (status === 429)
            return "Too many attempts. Please wait a minute and try again.";
        if (data.errors) return Object.values(data.errors)[0][0];

        return data.message || "Something went wrong. Please try again.";
    }

    async function confirmPayment(response) {
        confirming = true;
        setBusy(true);
        setStatus("Confirming your payment...");

        const failure =
            "We could not confirm your payment automatically. Please do not pay again. " +
            `Contact us with this reference: ${response.razorpay_payment_id}`;

        try {
            const result = await postJson(window.CONFIG.verifyUrl, {
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature,
            });

            if (result.ok && result.data.verified) {
                setStatus(
                    "JazakAllahu Khair! Your donation was received.",
                    "success",
                );
                resetForm();
            } else {
                setStatus(failure, "error");
            }
        } catch (e) {
            setStatus(failure, "error");
        } finally {
            confirming = false;
            setBusy(false);
        }
    }

    function openCheckout(order) {
        const rzp = new Razorpay({
            key: order.key,
            amount: order.amount,
            currency: "INR",
            name: "Taddabur",
            description: "Donation",
            order_id: order.order_id,
            theme: { color: "#1B5E3B" },
            modal: {
                ondismiss: () => {
                    if (!confirming) setBusy(false);
                },
            },
            handler: confirmPayment,
        });

        rzp.on("payment.failed", () => {
            setStatus(
                "The payment did not go through. If any amount was deducted, it is usually refunded by your bank within a few days.",
                "error",
            );
        });

        rzp.open();
    }

    donateBtn.addEventListener("click", async () => {
        const amount = Number(byId("donation-amount").value);
        const name = byId("donor-name").value.trim();
        const message = byId("donor-message").value.trim();
        const isPublic = byId("donor-public").checked;

        if (
            !Number.isInteger(amount) ||
            amount < MIN_AMOUNT ||
            amount > MAX_AMOUNT
        ) {
            setStatus(
                `Please enter a whole amount between ₹${MIN_AMOUNT} and ₹${MAX_AMOUNT.toLocaleString("en-IN")}.`,
                "error",
            );
            return;
        }

        if (typeof Razorpay === "undefined") {
            setStatus(
                "The payment service could not load. Please check your connection and refresh.",
                "error",
            );
            return;
        }

        setBusy(true);
        setStatus("");

        try {
            const order = await postJson(window.CONFIG.createOrderUrl, {
                amount,
                name,
                message,
                is_public: isPublic,
            });

            if (!order.ok) {
                setStatus(errorMessage(order), "error");
                setBusy(false);
                return;
            }

            openCheckout(order.data);
        } catch (e) {
            setStatus("Network problem. Please try again.", "error");
            setBusy(false);
        }
    });
})();
