<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'transactions']) ?>

    <main class="container py-5">
        <div class="mb-4">
            <span class="detail-heading-tag">Account</span>
            <h1 class="mt-3 mb-2">Transactions</h1>
            <!-- <p class="text-muted mb-0">Your payment history, newest first.</p> -->
        </div>

        <?php if (empty($transactions)): ?>
            <div class="panel-card p-4">
                <p class="text-muted mb-0">You don't have any transactions yet.</p>
            </div>
        <?php else: ?>
            <div class="panel-card p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Transaction ID</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Message / error</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <?php $status = (string) $transaction['payment_status']; ?>
                                <?php $createdAt = strtotime((string) ($transaction['created_at'] ?? '')); ?>
                                <tr>
                                    <td><?= esc($transaction['transactionid']) ?></td>
                                    <td>
                                        <?= $transaction['amount'] === null ? '—' : '₹' . number_format($transaction['amount'], 2) ?>
                                    </td>
                                    <td><?= esc($status === '0' ? 'Failed' : ($status === '1' ? 'Successful' : ucfirst($status))) ?></td>
                                    <td><?= esc($transaction['message'] ?: '—') ?></td>
                                    <td><?= $createdAt === false ? '—' : date('d-m-Y H:i', $createdAt) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>