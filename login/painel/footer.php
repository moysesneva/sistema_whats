                                </div><!-- end page-wrapper -->
                            </div><!-- end main-body -->
                        </div><!-- end pcoded-inner-content -->
                    </div><!-- end pcoded-content -->
                </div><!-- end pcoded-wrapper -->
            </div><!-- end pcoded-main-container -->

        </div><!-- end pcoded-container -->
    </div><!-- end pcoded -->

    <!-- Required Jquery -->
    <script type="text/javascript" src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../files/bower_components/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="../files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="../files/bower_components/modernizr/js/modernizr.js"></script>
    <!-- Chart js -->
    <script type="text/javascript" src="../files/bower_components/chart.js/js/Chart.js"></script>
    <!-- amchart js -->
    <script src="../files/assets/pages/widget/amchart/amcharts.js"></script>
    <script src="../files/assets/pages/widget/amchart/serial.js"></script>
    <script src="../files/assets/pages/widget/amchart/light.js"></script>
    <script src="../files/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="../files/assets/js/SmoothScroll.js"></script>
    <!-- custom js -->
    <script src="../files/assets/js/vartical-layout.min.js"></script>
    <script type="text/javascript" src="../files/assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="../files/assets/js/script.min.js"></script>
    <?php if (isset($js_extra)) echo $js_extra; ?>

    <!-- Configuração do aviso de expiração de sessão -->
    <script>
    window.SESSION_TIMEOUT_SEC   = <?= defined('SESSION_TIMEOUT') ? (int) SESSION_TIMEOUT : 1800 ?>;
    window.SESSION_WARN_SEC      = <?= defined('SESSION_TIMEOUT') ? min(300, max(60, (int) SESSION_TIMEOUT - 60)) : 240 ?>;
    window.SESSION_KEEPALIVE_URL = 'api/keepalive.php';
    window.SESSION_LOGIN_URL     = 'login_adm.php?expirado=1';
    </script>
    <script src="../files/assets/js/session-warning.js"></script>

    <!-- Modal: aviso de expiração de sessão -->
    <div class="modal fade" id="session-warning-modal" tabindex="-1" role="dialog" aria-labelledby="session-warning-title" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:440px;">
            <div class="modal-content" style="border:none;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.22);">
                <div class="modal-header" style="background:#001f3f;border-radius:12px 12px 0 0;padding:18px 24px;">
                    <h5 class="modal-title" id="session-warning-title" style="color:#fff;font-weight:700;font-size:1rem;margin:0;">
                        <i class="feather icon-clock" style="color:#FF5500;margin-right:8px;"></i>
                        Sessão prestes a expirar
                    </h5>
                </div>
                <div class="modal-body" style="padding:24px 24px 16px;text-align:center;">
                    <p style="color:#333;font-size:0.97rem;margin-bottom:8px;">
                        Sua sessão expirará em:
                    </p>
                    <p id="session-warning-countdown" style="font-size:2rem;font-weight:700;color:#FF5500;margin:0 0 16px;">
                        5m 0s
                    </p>
                    <p style="color:#666;font-size:0.87rem;margin:0;">
                        Clique em <strong>Continuar sessão</strong> para permanecer conectado ou em <strong>Sair</strong> para encerrar agora.
                    </p>
                    <p id="session-warning-status" style="display:none;font-size:0.85rem;margin-top:10px;font-weight:600;"></p>
                </div>
                <div class="modal-footer" style="border:none;padding:12px 24px 20px;justify-content:center;gap:12px;">
                    <button type="button" id="session-warning-btn-continuar"
                            style="background:#FF5500;color:#fff;border:none;border-radius:8px;padding:10px 28px;font-weight:700;font-size:0.95rem;cursor:pointer;transition:opacity .2s;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="feather icon-refresh-cw" style="margin-right:6px;"></i>Continuar sessão
                    </button>
                    <button type="button" id="session-warning-btn-sair"
                            style="background:#fff;color:#001f3f;border:2px solid #001f3f;border-radius:8px;padding:9px 22px;font-weight:600;font-size:0.95rem;cursor:pointer;transition:opacity .2s;"
                            onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                        <i class="feather icon-log-out" style="margin-right:6px;"></i>Sair
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<?php
include 'pcoded.php';
include 'erro.php';
?>
