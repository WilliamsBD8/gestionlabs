<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <!--card stats start-->
                    <div id="card-stats" class="pt-0">
                        <h4 class="center-align">Portal de Clientes Gestion Labs</h4>
                        <div class="row">
                            <div class="col s12 m6 l6">
                                <div class="card purple lighten-1 gradient-shadow white-text animate fadeLeft">
                                            <div class="content_head">
                                                <div>
                                                    <i class="fas fa-file-signature"></i>
                                                    <span>Solicitudes</span>
                                                </div>
                                                <span class="white-text"><?=$solicitudes?></span>
                                            </div>
                                            <div class="card-action purple darken-1 center-aling">
                                                <span>Este mes: <?= $total_mes ?></span>
                                            </div>
                                </div>
                            </div>
                            <div class="col s12 m6 l6">
                                <div class="card deep-orange lighten-1 gradient-shadow min-height-100 white-text animate fadeRight">
                                    <div class="padding-4">
                                        <div class="row">
                                            <div class="col s12 m12">
                                                <div class="content_head">
                                                    <div>
                                                        <i class="fas fa-project-diagram"></i>
                                                        <span>En proceso</span>
                                                    </div>
                                                    <span class="mb-0 white-text"><?= $pendientes ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--card stats end-->
                    <!--yearly & weekly revenue chart start-->
                    <div id="sales-chart">
                        <div class="row">
                            <div class="col s12 m12 l8">
                                <div id="revenue-chart" class="card animate fadeUp">
                                    <div class="card-content">
                                        <h4 class="header mt-0">
                                            Historial de solicitudes
                                            <a href="<?= base_url(['amc-laboratorio/reporte']) ?>" class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow right">Más</a>
                                        </h4>
                                        <div class="row">
                                            <div class="col s12">
                                                <div class="yearly-revenue-chart">
                                                    <canvas id="thisYearRevenue" class="firstShadow"
                                                            height="350"></canvas>
                                                    <canvas id="lastYearRevenue" height="350"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12 m12 l4">
                                <div id="weekly-earning" class="card animate fadeUp">
                                    <div class="card-content">
                                        <h4 class="header m-0">Ensayos recientes
                                        </h4>
                                        <div class="row">
                                            <?php foreach ($ensayos_r as $key => $value): ?>
                                                <div class="col s12  card-content reports">
                                                    <div class="row">
                                                        <div class="col s12">
                                                            <div class="left truncate">
                                                                <i class="far fa-check-circle"></i>
                                                                <div class="truncate">
                                                                    <p>Informe #<?=$value->certificado_nro?></p>
                                                                    <span class="truncate <?= (strlen($value->producto) > 32) ? 'tooltipped' : '' ?>" data-position="left" data-tooltip="<?= (strlen($value->producto) > 32) ? $value->producto : ''?>">
                                                                        <?= $value->producto ?>
                                                                        </span>
                                                                    <br>
                                                                    <span class=""><?= $value->lote ?></span>
                                                                </div>
                                                            </div>
                                                            <form action="<?= base_url(['certificado', 'download']) ?>" method="POST">
                                                                <input type="hidden" name="certificado_reporte[]" value="<?= $value->certificado_nro ?>">
                                                                <button class="tooltipped download_home" data-position="left" data-tooltip="Descargar"><i class="far fa-file-pdf"></i></button>
                                                            </form>
                                                        </div>    
                                                    </div>
                                                </div>
                                                <br>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--yearly & weekly revenue chart end-->
                    <!-- Member online, Currunt Server load & Today's Revenue Chart start -->
                    <!-- <div id="daily-data-chart">
                        <div class="row">
                            <div class="col s12 m4 l4">
                                <div class="card pt-0 pb-0 animate fadeLeft">
                                    <div class="dashboard-revenue-wrapper padding-2 ml-2">
                                        <span class="new badge gradient-45deg-light-blue-cyan gradient-shadow mt-2 mr-2">+ 42.6%</span>
                                        <p class="mt-2 mb-0">Members online</p>
                                        <p class="no-margin grey-text lighten-3">360 avg</p>
                                        <h5>3,450</h5>
                                    </div>
                                    <div class="sample-chart-wrapper"
                                         style="margin-bottom: -14px; margin-top: -75px;">
                                        <canvas id="custom-line-chart-sample-one" class="center"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12 m4 l4 animate fadeUp">
                                <div class="card pt-0 pb-0">
                                    <div class="dashboard-revenue-wrapper padding-2 ml-2">
                                        <span class="new badge gradient-45deg-purple-deep-orange gradient-shadow mt-2 mr-2">+ 12%</span>
                                        <p class="mt-2 mb-0">Current server load</p>
                                        <p class="no-margin grey-text lighten-3">23.1% avg</p>
                                        <h5>+2500</h5>
                                    </div>
                                    <div class="sample-chart-wrapper"
                                         style="margin-bottom: -14px; margin-top: -75px;">
                                        <canvas id="custom-line-chart-sample-two" class="center"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12 m4 l4">
                                <div class="card pt-0 pb-0 animate fadeRight">
                                    <div class="dashboard-revenue-wrapper padding-2 ml-2">
                                        <span class="new badge gradient-45deg-amber-amber gradient-shadow mt-2 mr-2">+ $900</span>
                                        <p class="mt-2 mb-0">Today's revenue</p>
                                        <p class="no-margin grey-text lighten-3">$40,512 avg</p>
                                        <h5>$ 22,300</h5>
                                    </div>
                                    <div class="sample-chart-wrapper"
                                         style="margin-bottom: -14px; margin-top: -75px;">
                                        <canvas id="custom-line-chart-sample-three" class="center"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div><!-- START RIGHT SIDEBAR NAV -->
                <!-- END RIGHT SIDEBAR NAV -->
            </div>
            <div class="content-overlay"></div>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>
<script>
    console.log('holis');
    $(document).ready(function(){
        $('.tooltipped').tooltip();
        var historial = <?php echo json_encode($historial,JSON_FORCE_OBJECT); ?>;
        var total_historial = <?php echo json_encode(count($historial),JSON_FORCE_OBJECT); ?>;
        var total = [];
        var mes = [];
        for(var i = 0; i <= (total_historial-1); i++){
            total[i]  = parseInt(historial[i]['total']);
            mes[i]    = historial[i]['mes'];
        }
          // // Line chart with color shadow: Revenue for 2018 Chart
   var thisYearctx = document.getElementById("thisYearRevenue").getContext("2d");

   // Chart shadow LineAlt
   Chart.defaults.LineAlt = Chart.defaults.line;
   var draw = Chart.controllers.line.prototype.draw;
   var custom = Chart.controllers.line.extend({
      draw: function() {
         draw.apply(this, arguments);
         var ctx = this.chart.chart.ctx;
         var _stroke = ctx.stroke;
         ctx.stroke = function() {
            ctx.save();
            ctx.shadowColor = "rgba(156, 46, 157,0.5)";
            ctx.shadowBlur = 20;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 20;
            _stroke.apply(this, arguments);
            ctx.restore();
         };
      }
   });
   Chart.controllers.LineAlt = custom;

   // Chart shadow LineAlt2
   Chart.defaults.LineAlt2 = Chart.defaults.line;
   var draw = Chart.controllers.line.prototype.draw;
   var custom = Chart.controllers.line.extend({
      draw: function() {
         draw.apply(this, arguments);
         var ctx = this.chart.chart.ctx;
         var _stroke = ctx.stroke;
         ctx.stroke = function() {
            ctx.save();
            _stroke.apply(this, arguments);
            ctx.restore();
         };
      }
   });
   Chart.controllers.LineAlt2 = custom;

   var thisYearData = {
      labels: mes,
      datasets: [
         {
            label: "Total",
            data: total,
            fill: false,
            pointRadius: 2.2,
            pointBorderWidth: 1,
            borderColor: "#9C2E9D",
            borderWidth: 5,
            pointBorderColor: "#9C2E9D",
            pointHighlightFill: "#9C2E9D",
            pointHoverBackgroundColor: "#9C2E9D",
            pointHoverBorderWidth: 2
         }
      ]
   };

   var thisYearOption = {
      responsive: true,
      maintainAspectRatio: true,
      datasetStrokeWidth: 3,
      pointDotStrokeWidth: 4,
      tooltipFillColor: "rgba(0,0,0,0.6)",
      legend: {
         display: false,
         position: "bottom"
      },
      hover: {
         mode: "label"
      },
      scales: {
         xAxes: [
            {
               display: true
            }
         ],
         yAxes: [
            {
               ticks: {
                  padding: 10,
                  stepSize: 1,
                  max: 5,
                  min: 0,
                  fontColor: "#9e9e9e"
               },
               gridLines: {
                  display: true,
                  drawBorder: false,
                  lineWidth: 1,
                  zeroLineColor: "#e5e5e5"
               }
            }
         ]
      },
      title: {
         display: false,
         fontColor: "#FFF",
         fullWidth: false,
         fontSize: 40,
         text: "82%"
      }
   };

   var thisYearChart = new Chart(thisYearctx, {
      type: "LineAlt",
      data: thisYearData,
      options: thisYearOption
   });

    })
</script>