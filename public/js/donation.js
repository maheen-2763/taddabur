// Toggle logic — top level, page load pe hi register hota hai
document.querySelectorAll(".payment-toggle-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
        document
            .querySelectorAll(".payment-toggle-btn")
            .forEach((b) => b.classList.remove("active"));
        document
            .querySelectorAll(".payment-panel")
            .forEach((p) => p.classList.add("d-none"));

        this.classList.add("active");
        document.getElementById(this.dataset.target).classList.remove("d-none");
    });
});

// Donate button logic — alag, top level
document
    .getElementById("razorpay-donate-btn")
    .addEventListener("click", function () {
        const amount = document.getElementById("donation-amount").value;
        const name = document.getElementById("donor-name").value;
        const message = document.getElementById("donor-message").value;
        const isPublic = document.getElementById("donor-public").checked;

        if (!amount || amount < 1) {
            alert("Please enter a valid amount");
            return;
        }

        fetch(window.CONFIG.createOrderUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.CONFIG.csrfToken,
            },
            body: JSON.stringify({
                amount,
                name,
                message,
                is_public: isPublic,
            }),
        })
            .then((res) => res.json())
            .then((data) => {
                const options = {
                    key: data.key,
                    amount: data.amount,
                    currency: "INR",
                    name: "Taddabur",
                    description: "Donation",
                    order_id: data.order_id,
                    handler: function (response) {
                        fetch(window.CONFIG.verifyUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": window.CONFIG.csrfToken,
                            },
                            body: JSON.stringify({
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id:
                                    response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature,
                            }),
                        })
                            .then((res) => res.json())
                            .then((result) => {
                                if (result.verified) {
                                    alert(
                                        "JazakAllahu Khair! Your donation was successful.",
                                    );
                                    location.reload();
                                } else {
                                    alert(
                                        "Payment wasn't verified. Kindly contact Support team .",
                                    );
                                }
                            });
                    },
                    theme: { color: "#0d6efd" },
                };

                const rzp = new Razorpay(options);
                rzp.open();
            })
            .catch(() => alert("Something went wrong, Please try again."));
    });
