<?php
require_once "/var/www/portal.twe.tech/includes/inc_all_reports.php";
validateAccountantRole();

// Fetch accounts data
$sql = "SELECT accounts.*, account_types.account_type_parent 
        FROM accounts 
        LEFT JOIN account_types ON accounts.account_type = account_types.account_type_id 
        WHERE account_archived_at IS NULL 
        ORDER BY account_name ASC;";
$result = mysqli_query($mysqli, $sql);

$accounts = [];
$total_assets = 0;
$total_liabilities = 0;
$total_equity = 0;
$currency_code = '';

while ($row = mysqli_fetch_assoc($result)) {
    $account_id = $row['account_id'];

    // Fetch and calculate balances
    $balance = calculateAccountBalance($account_id);

    // Categorize account based on type
    if ($row['account_type_parent'] == 1) {
        $total_assets += $balance;
    } elseif ($row['account_type_parent'] == 2) {
        $total_liabilities += $balance;
    } elseif ($row['account_type_parent'] == 3) {
        $total_equities += $balance;
    }

    // Add account to array
    $accounts[$row['account_type_parent']][] = [
        'id' => $account_id,
        'name' => $row['account_name'],
        'type' => $row['account_type_name'],
        'balance' => $balance
    ];
}

function display_account_section($mysqli, $accounts, $type) {
    foreach ($accounts[$type] as $account) {
        global $currency_format;
        global $currency_code;
        $currency_code = getAccountCurrencyCode($account['id']);
        echo "<tr>";
        echo "<td>{$account['type']}</td>";
        echo "<td>{$account['name']}</td>";
        echo "<td class='text-right'>" . numfmt_format_currency($currency_format, $account['balance'], $currency_code) . "</td>";
        echo "</tr>";
    }
}

?>

<div class="card">
    <div class="card-header py-2">
        <h3 class="card-title mt-2"><i class="fas fa-fw fa-balance-scale mr-2"></i>Balance Sheet</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-label-primary d-print-none" onclick="window.print();"><i class="fas fa-fw fa-print mr-2"></i>Print</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="card-datatable table-responsive container-fluid  pt-0">
            <div class="text-center">
                <h2 class="text-dark">
                    <?= nullable_htmlentities($company_name); ?>
                </h2>
                <h3 class="text-dark">Balance Sheet</h3>
                <h5 class="text-dark">As of <?= date("F j, Y"); ?></h5>
            </div>
            <div>
            <table id=responsive class="responsive table table-sm">
            <!-- Table Header -->
            <!-- Table Body -->
            <tbody>
                <!-- Assets Section -->
                <?php display_account_section($mysqli, $accounts, 1); ?>
                <tr>
                    <th></th>
                    <th class="text-uppercase">Total Assets</th>
                    <th class="text-right"><?= numfmt_format_currency($currency_format, $total_assets, $currency_code); ?></th>
                </tr>

                <!-- Liabilities Section -->
                <?php display_account_section($mysqli, $accounts, 2); ?>
                <tr>
                    <th></th>
                    <th class="text-uppercase">Total Liabilities</th>
                    <th class="text-right"><?= numfmt_format_currency($currency_format, $total_liabilities, $currency_code); ?></th>
                </tr>

                <!-- Equities Section -->
                <?php display_account_section($mysqli, $accounts, 3); ?>
                <tr>
                <th></th>
                    <th class="text-uppercase">Total Equities</th>
                    <th class="text-right"><?= numfmt_format_currency($currency_format, $total_equities, $currency_code); ?></th>
                </tr>
            </tbody>
        </table>

            </div>
        </div>
    </div>

    <?php require_once '/var/www/portal.twe.tech/includes/footer.php';    ?>