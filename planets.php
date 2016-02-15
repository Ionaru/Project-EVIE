<?php ob_start(); ?>
<?php include __DIR__ . 'head.php'; ?>
<?php include __DIR__ . 'nav.php'; ?>

    <div class="row" id="planets"></div>
    <div class="row" id="planetsNew"></div>

<?php include __DIR__ . 'foot.php'; ?>

    <script type="text/javascript" src="js/planetJS.js"></script>
    </body>
    </html>
<?php ob_flush(); ?>