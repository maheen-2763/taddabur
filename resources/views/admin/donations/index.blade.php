@extends('admin.layout')
@section('title', 'Donations')

@section('content')
    <div class="mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">Donations</h5>
        <small class="text-muted">Voluntary support via Razorpay · only "{{ $paidStatus }}" payments count as
            received</small>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Total received',
                'value' => '₹' . number_format($summary['paidTotal'], 2),
                'icon' => 'heart',
                'tone' => 'gold',
                'note' => $summary['paidCount'] . ' Successful',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Average donation',
                'value' => '₹' . number_format($summary['average'], 2),
                'icon' => 'cash-coin',
                'tone' => 'gold',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'All attempts',
                'value' => number_format($summary['attempts']),
                'icon' => 'receipt',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Not completed',
                'value' => number_format($summary['attempts'] - $summary['paidCount']),
                'icon' => 'hourglass-split',
                'note' => 'pending or failed',
            ])
        </div>
    </div>

    <form method="GET" class="admin-card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Name, order ID or payment ID" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select form-select-sm">
                    <option value="">All methods</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m }}" @selected(request('method') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm"
                    value="{{ request('from') }}"></div>
            <div class="col-md-2"><input type="date" name="to" class="form-control form-control-sm"
                    value="{{ request('to') }}"></div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-admin-primary btn-sm">Go</button>
                <a href="{{ route('admin.donations.index') }}" class="btn btn-light btn-sm">Reset</a>
            </div>
        </div>
        @error('to')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </form>

    <div class="text-muted mb-2" style="font-size:.78rem">
        {{ number_format($donors->total()) }} records · ₹{{ number_format($filtered['paidTotal'], 2) }} received in this
        view
    </div>

    <div class="admin-table">
        <table class="table table-hover align-middle" style="font-size:.82rem">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Order ID</th>
                    <th>Payment ID</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donors as $d)
                    <tr>
                        <td class="text-nowrap text-muted">
                            {{ $d->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                        <td>
                            {{ $d->name ?: 'Anonymous' }}
                            @if ($d->is_public)
                                <span class="badge bg-info-subtle text-info" title="Name shown publicly">Public</span>
                            @endif
                        </td>
                        <td class="fw-medium" style="color:var(--emerald)">₹{{ number_format($d->amount, 2) }}</td>
                        <td class="text-muted">{{ $d->payment_method ?? '—' }}</td>
                        <td>
                            <span
                                class="badge {{ match ($d->status) {
                                    $paidStatus => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary',
                                } }}">{{ ucfirst($d->status ?? '—') }}</span>
                        </td>
                        <td style="font-family:monospace; font-size:.72rem">{{ $d->razorpay_order_id ?? '—' }}</td>
                        <td style="font-family:monospace; font-size:.72rem">{{ $d->razorpay_payment_id ?? '—' }}</td>
                        <td class="text-muted" style="max-width:220px">{{ $d->message ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No donations match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $donors->links() }}</div>
    </div>
@endsection
