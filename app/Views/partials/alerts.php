<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php $errors = session()->getFlashdata('errors'); ?>
<?php if (! empty($errors) && is_array($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0 pl-3">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
