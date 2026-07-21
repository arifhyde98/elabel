<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label Box <?= esc((string) $box['box_code']) ?></title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        html,
        body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #fff;
            position: relative;
        }
        .sheet {
            width: 100%;
            max-width: 194mm;
            height: 164mm;
            margin: 0 auto;
            border: 3px solid #111;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .header {
            display: grid;
            grid-template-columns: 110px 1fr 160px;
            gap: 8px;
            align-items: start;
            padding: 12px 14px 10px;
            border-bottom: 3px solid #111;
        }
        .logo {
            width: 90px;
            height: 115px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .org {
            padding-top: 4px;
        }
        .org h1,
        .org h2,
        .org h3 {
            margin: 0;
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: 0.2px;
        }
        .org h1 {
            font-size: 17px;
            margin-bottom: 5px;
        }
        .org h2 {
            font-size: 19px;
            margin-bottom: 5px;
        }
        .org h3 {
            font-size: 22px;
        }
        .qr-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 6px;
            padding-right: 10px;
            justify-self: end;
        }
        .qr {
            width: 78px;
            height: 78px;
            display: grid;
            place-items: center;
        }
        .qr img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .box-code {
            display: none;
        }
        .content {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: repeat(var(--label-columns, 4), minmax(0, 1fr));
        }
        .column {
            min-width: 0;
            padding: 10px 14px 10px;
            border-right: 3px solid #111;
            box-sizing: border-box;
        }
        .column:last-child {
            border-right: 0;
        }
        .year-title {
            color: #e1251b;
            font-weight: 800;
            font-size: var(--year-title-size, 18px);
            line-height: 1;
            margin-bottom: 8px;
        }
        .plate-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            font-size: var(--plate-font-size, 19px);
            line-height: var(--plate-line-height, 1.26);
            margin-bottom: 3px;
            white-space: nowrap;
        }
        .plate-no {
            min-width: 28px;
            text-align: right;
            font-weight: 500;
        }
        .plate-text {
            font-weight: 800;
        }
        .muted {
            font-size: 18px;
            font-weight: 600;
            padding: 16px 20px;
        }
        .footer {
            border-top: 3px solid #111;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: start;
            padding: 10px 14px 12px;
        }
        .footer-title {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 6px;
        }
        .footer-years {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.1;
        }
        .footer-count {
            text-align: right;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.15;
        }
        .print-date {
            width: 78px;
            margin-top: 4px;
            font-size: 9px;
            line-height: 1.2;
            color: #111;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }
        @media print {
            html,
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
            }
            .sheet {
                margin-top: 0;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
<?php
    $logoPath = FCPATH . 'Assets/logo.png';
    $logoSrc = base_url('Assets/logo.png');
    if (is_file($logoPath)) {
        $logoData = base64_encode((string) file_get_contents($logoPath));
        if ($logoData !== '') {
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }
    }

    $grouped = [];
    foreach ($items as $item) {
        $yearKey = $item['year'] ?: 'Tidak diketahui';
        $grouped[$yearKey][] = $item['plate_number'];
    }
    ksort($grouped);

    $yearCounts = [];
    $total = 0;
    foreach ($grouped as $year => $plates) {
        $yearCounts[$year] = count($plates);
        $total += count($plates);
    }

    $boxYearList = [];
    if (! empty($boxYears)) {
        foreach ($boxYears as $row) {
            $boxYearList[] = (string) $row['year'];
        }
        sort($boxYearList);
    }

    $years = ! empty($boxYearList) ? $boxYearList : array_keys($yearCounts);
    $yearRange = '';
    if (! empty($years)) {
        $minYear = min($years);
        $maxYear = max($years);
        $yearRange = $minYear === $maxYear ? (string) $minYear : $minYear . '-' . $maxYear;
    }

    $vehicleLabel = ($box['vehicle_type'] ?? '') === 'R2' ? 'BPKB KENDARAAN RODA 2' : 'BPKB KENDARAAN RODA 4';
    $yearsList = implode(', ', $years);
    $qrPayload = trim(implode("\n", array_filter([
        'Box: ' . ($box['box_code'] ?? ''),
        'Lokasi: ' . ($box['location'] ?? '-'),
        'Jenis: ' . $vehicleLabel,
        'Tahun: ' . ($yearsList !== '' ? $yearsList : '-'),
        'Total: ' . $total,
    ])));
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . rawurlencode($qrPayload);

    $boxDisplayCode = (string) ($box['box_code'] ?? '');
    if (preg_match('/^[A-Z0-9]+-(\d+)$/', $boxDisplayCode, $matches) === 1) {
        $boxDisplayCode = (string) ((int) $matches[1]);
    }

    $lines = [];
    foreach ($grouped as $year => $plates) {
        $lines[] = [
            'type' => 'year',
            'year' => (string) $year,
        ];
        foreach ($plates as $idx => $plate) {
            $lines[] = [
                'type' => 'plate',
                'number' => $idx + 1,
                'plate' => (string) $plate,
            ];
        }
    }

    $maxLinesPerColumn = $total >= 60 ? 18 : 15;
    $columns = [];
    $currentColumn = [];
    foreach ($lines as $line) {
        $currentLineCount = count($currentColumn);
        $startsNewYear = $line['type'] === 'year';
        if (
            $currentLineCount >= $maxLinesPerColumn
            || ($startsNewYear && $currentLineCount > 0 && $currentLineCount >= $maxLinesPerColumn - 1)
        ) {
            $columns[] = $currentColumn;
            $currentColumn = [];
        }
        $currentColumn[] = $line;
    }
    if ($currentColumn !== []) {
        $columns[] = $currentColumn;
    }

    $columnCount = max(1, count($columns));
    $plateFontSize = '17px';
    $plateLineHeight = '1.18';
    $yearTitleSize = '16px';
    if ($columnCount === 3) {
        $plateFontSize = '18px';
        $plateLineHeight = '1.22';
        $yearTitleSize = '17px';
    } elseif ($columnCount >= 4) {
        $plateFontSize = '17px';
        $plateLineHeight = '1.18';
        $yearTitleSize = '16px';
    }
    if ($total >= 60) {
        $plateFontSize = '15px';
        $plateLineHeight = '1.12';
        $yearTitleSize = '15px';
    }

    $printDate = date('d/m/Y');
?>
    <div class="sheet" style="--label-columns: <?= esc((string) $columnCount) ?>; --plate-font-size: <?= esc($plateFontSize) ?>; --plate-line-height: <?= esc($plateLineHeight) ?>; --year-title-size: <?= esc($yearTitleSize) ?>;">
        <div class="header">
            <div class="logo">
                <img src="<?= esc($logoSrc) ?>" alt="Logo">
            </div>
            <div class="org">
                <h1>Pemerintah Kabupaten Donggala</h1>
                <h2>Badan Pengelolaan Keuangan dan Aset Daerah</h2>
                <h3>Bidang Aset</h3>
            </div>
            <div class="qr-wrap">
                <div class="qr">
                    <img src="<?= esc($qrUrl) ?>" alt="QR Box <?= esc((string) $box['box_code']) ?>">
                </div>
                <div class="print-date"><?= esc($printDate) ?></div>
                <div class="box-code">BOX <?= esc($boxDisplayCode) ?></div>
            </div>
        </div>

        <div class="content">
            <?php if (empty($items)): ?>
                <p class="muted">Tidak ada data BPKB.</p>
            <?php else: ?>
                <?php foreach ($columns as $column): ?>
                    <div class="column">
                        <?php foreach ($column as $line): ?>
                            <?php if ($line['type'] === 'year'): ?>
                                <div class="year-title"><?= esc($line['year']) ?></div>
                            <?php else: ?>
                                <div class="plate-row">
                                    <span class="plate-no"><?= esc((string) $line['number']) ?>.</span>
                                    <span class="plate-text"><?= esc($line['plate']) ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="footer">
            <div>
                <div class="footer-title"><?= esc($vehicleLabel) ?></div>
                <div class="footer-years">
                    <?php if (! empty($years)): ?>
                        <?php foreach ($years as $year): ?>
                            <span><?= esc((string) $year) ?> =<?= esc((string) ($yearCounts[$year] ?? 0)) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($yearCounts as $year => $count): ?>
                            <span><?= esc((string) $year) ?> =<?= esc((string) $count) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="footer-count">
                <div><?= esc($yearRange) ?></div>
                <div>Total = <?= esc((string) $total) ?></div>
            </div>
        </div>
    </div>
</body>
<script>
    window.addEventListener('load', function () {
        if (window.self !== window.top || window.location.search.indexOf('autoprint=1') !== -1) {
            window.print();
        }
    });
</script>
</html>
