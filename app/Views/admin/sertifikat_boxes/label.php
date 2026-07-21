<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label Box Sertipikat <?= esc((string) $box['box_code']) ?></title>
    <?php
        $itemCount = count($items);
        $compactMode = $itemCount >= 27;
        $printDate = date('d/m/Y');
        $logoPath = FCPATH . 'Assets/logo.png';
        $logoSrc = base_url('Assets/logo.png');
        if (is_file($logoPath)) {
            $logoData = base64_encode((string) file_get_contents($logoPath));
            if ($logoData !== '') {
                $logoSrc = 'data:image/png;base64,' . $logoData;
            }
        }
    ?>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #111;
            background: #fff;
        }

        .sheet {
            width: 200mm;
            min-height: 287mm;
            margin: 5mm auto;
            border: 2px solid #111;
            box-sizing: border-box;
            padding: <?= $compactMode ? '5mm 5mm' : '6mm 6mm' ?>;
            position: relative;
        }

        .label-brand {
            position: absolute;
            top: <?= $compactMode ? '5mm' : '6mm' ?>;
            left: <?= $compactMode ? '5mm' : '6mm' ?>;
            width: <?= $compactMode ? '16mm' : '18mm' ?>;
            text-align: center;
            font-size: <?= $compactMode ? '6px' : '7px' ?>;
            line-height: 1.05;
            font-weight: 700;
            text-transform: uppercase;
        }

        .label-logo {
            width: <?= $compactMode ? '14mm' : '16mm' ?>;
            height: <?= $compactMode ? '14mm' : '16mm' ?>;
            object-fit: contain;
            display: block;
            margin: 0 auto 1mm;
        }

        .header {
            border-bottom: 2px solid #111;
            padding-bottom: <?= $compactMode ? '2mm' : '3mm' ?>;
            margin-bottom: <?= $compactMode ? '2mm' : '3mm' ?>;
            padding-left: <?= $compactMode ? '21mm' : '24mm' ?>;
            min-height: <?= $compactMode ? '18mm' : '20mm' ?>;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header h1 {
            margin: 0 0 1.2mm;
            font-size: <?= $compactMode ? '13px' : '15px' ?>;
            line-height: 1.25;
        }

        .header .meta {
            margin: 0.4mm 0;
            font-size: <?= $compactMode ? '9px' : '10px' ?>;
            line-height: <?= $compactMode ? '1.15' : '1.25' ?>;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #111;
            padding: <?= $compactMode ? '0.95mm 1.2mm' : '1.3mm 1.6mm' ?>;
            font-size: <?= $compactMode ? '7.4px' : '8.5px' ?>;
            line-height: <?= $compactMode ? '1.05' : '1.15' ?>;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background: #f3f4f6;
            font-size: <?= $compactMode ? '7px' : '8px' ?>;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .col-no {
            width: 8%;
            text-align: center;
        }

        .col-sertipikat {
            width: 24%;
        }

        .col-status {
            width: 24%;
        }

        .col-alamat {
            width: 44%;
        }

        .footer {
            border-top: 2px solid #111;
            margin-top: <?= $compactMode ? '2mm' : '3mm' ?>;
            padding-top: <?= $compactMode ? '1.5mm' : '2mm' ?>;
            display: flex;
            justify-content: space-between;
            gap: 6mm;
            font-size: <?= $compactMode ? '9px' : '10px' ?>;
            font-weight: 700;
        }

        .footer-total {
            text-align: right;
        }

        .print-date {
            margin-top: 1mm;
            font-size: <?= $compactMode ? '8px' : '9px' ?>;
            line-height: 1.1;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="label-brand">
            <img src="<?= esc($logoSrc) ?>" alt="Logo Kabupaten Donggala" class="label-logo">
            <div>Kab. Donggala</div>
        </div>
        <div class="header">
            <h1>LABEL BOX SERTIPIKAT TANAH</h1>
            <div class="meta">Kode Box: <?= esc((string) $box['box_code']) ?></div>
            <div class="meta">Lokasi: <?= esc((string) ($box['lokasi'] ?? '-')) ?></div>
            <div class="meta">Total Sertipikat: <?= esc((string) count($items)) ?> / <?= esc((string) $maxPerBox) ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-sertipikat">No. Sertipikat</th>
                    <th class="col-status">Status Penggunaan</th>
                    <th class="col-alamat">Alamat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Belum ada data sertipikat.</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="col-no"><?= $i++ ?></td>
                            <td class="col-sertipikat"><?= esc((string) ($item['no_sertipikat'] ?? '-')) ?></td>
                            <td class="col-status"><?= esc((string) ($item['status_penggunaan'] ?? '-')) ?></td>
                            <td class="col-alamat"><?= esc((string) ($item['alamat'] ?? '-')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div><?= esc((string) $box['box_code']) ?></div>
            <div class="footer-total">
                <div>Total = <?= esc((string) count($items)) ?></div>
                <div class="print-date"><?= esc($printDate) ?></div>
            </div>
        </div>
    </div>
</body>
</html>
