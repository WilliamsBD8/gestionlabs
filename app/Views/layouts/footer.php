<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;z-index:10;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
<script src="<?= base_url() ?>/assets/sweetAlert/dist/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.min.js"></script>
<script src="<?= base_url() ?>/assets/js/chart.min.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/dashboard-ecommerce.js"></script>

<script src="<?= base_url() ?>/grocery-crud/js/libraries/ckeditor/ckeditor.adapter-jquery.js"></script>
<script src="<?= base_url() ?>/grocery-crud/js/libraries/jquery-ui.js"></script>
<script src="<?= base_url() ?>/grocery-crud/js/build/grocery-crud-v2.8.1.0659b25.js"></script>
<script src="<?= base_url() ?>/grocery-crud/js/build/load-grocery-crud.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){
            document.body.style.cursor='default';
        }, 1000);
    });
</script>
</body>
</html>

