<?php

?>
<!DOCTYPE HTML>
<html>
<head>
	<style>
      
        /*#chart-candlestick {
	      max-width: 50%;
	      margin: 35px auto;
	    }

	    #chart-bar {
	      max-width: 50%;
	      margin: 35px auto;
	    }*/
      
    </style>

    <link href="<?=WEB_PATH?>plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
    <link href="<?=WEB_PATH?>plugins/apex/assets/styles.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="<?=WEB_PATH?>plugins/apex/apexcharts.js"></script>
    
	<!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
	<!-- <script src="<?=WEB_PATH?>plugins/apex/assets/ohlc.js"></script> -->
</head>
<body>
	<div id= "chart-candlestick"></div>
	<div id= "chart-bar"></div>

	<script>
		var options = {
						chart: {
						    type: 'line',
						    id: 'candles',
						},
						series: [  {
							          name: 'line',
							          type: 'line',
							          data: <?=$candle_line_chart?>
							  
							        },
							        {
							          name: 'line_2',
							          type: 'line',
							          data: <?=$candle_line_chart_2?>
							  
							        },
							         {
							          name: 'line_3',
							          type: 'line',
							          data: <?=$candle_line_chart_3?>
							  
							        },{
						  	           name: 'price',
						  	           type: 'candlestick',
						               data: <?=$candle_chart?>
           						}],
					        //     options: {
								     //          chart: {
								     //            type: 'candlestick',
								     //            height: 350
								     //          },
								     //          // title: {
								     //          //   text: 'CandleStick Chart',
								     //          //   align: 'left'
								     //          // },
								     //          xaxis: {
								     //             type: 'datetime'
								     //          },
								     //          yaxis: {
								     //            tooltip: {
								     //              enabled: true
								     //            }
								     //          },
								     //          plotOptions: {
													//   candlestick: {
													//     wick: {
													//       useFillColor: true,
													//     }
													//   }
													// }
					        //     		},
			              xaxis: {
			                  type: 'datetime',
			                  tickPlacement: 'between',
			                  labels: {
					            show: false
					          }
			              },
			              yaxis: {
						                tooltip: {
						                  enabled: true
						      			},
						      			labels: {
										    formatter: function (value) {
										      return value.toFixed(4);
										    }
										}
			             },
			              tooltip: {
							          x: {
							            format: 'dd MMM yyyy HH:mm:ss'
							          }
							        },
						   zoom: {
							          enabled: true,
							          type: 'x',  
							          autoScaleYaxis: false,  
							          zoomedArea: {
							            fill: {
							              color: '#90CAF9',
							              opacity: 0.4
							            },
							            stroke: {
							              color: '#0D47A1',
							              opacity: 0.4,
							              width: 1
							            }
							          }
							      },

					        stroke: {
					           curve: 'smooth',
					           width: [1,1,1,2]
					        },
					        tooltip: {
						          shared: true,
						          custom: [function({seriesIndex, dataPointIndex, w}) {
						            return w.globals.series[seriesIndex][dataPointIndex]
						          },function({seriesIndex, dataPointIndex, w}) {
						            return w.globals.series[seriesIndex][dataPointIndex]
						          },function({seriesIndex, dataPointIndex, w}) {
						            return w.globals.series[seriesIndex][dataPointIndex]
						          }, function({ seriesIndex, dataPointIndex, w }) {
						            var o = w.globals.seriesCandleO[seriesIndex][dataPointIndex]
						            var h = w.globals.seriesCandleH[seriesIndex][dataPointIndex]
						            var l = w.globals.seriesCandleL[seriesIndex][dataPointIndex]
						            var c = w.globals.seriesCandleC[seriesIndex][dataPointIndex]
						            return (
						              '<div class="apexcharts-tooltip-candlestick">' +
						              '<div>Open: <span class="value">' +
						              o +
						              '</span></div>' +
						              '<div>High: <span class="value">' +
						              h +
						              '</span></div>' +
						              '<div>Low: <span class="value">' +
						              l +
						              '</span></div>' +
						              '<div>Close: <span class="value">' +
						              c +
						              '</span></div>' +
						              '</div>'
						            )
						          }]
					        },
					        legend: {
      									show: true,
      									position: 'top',
      									horizontalAlign: 'left',
      									fontSize: '16px',
      									fontWeight: 100,
      									labels: {
										          //colors: undefined,
										          useSeriesColors: true
										      },
										formatter: function(seriesName, opts) {

														var globals = opts.w.globals.maxY;
												
														if (seriesName == 'line'){
															return "MA5";
														} else if (seriesName == 'line_2'){
															return "MA10";
														} else if (seriesName == 'line_3'){
															return "MA30";
														}else if (seriesName == 'price'){
															return "Market";
														} else {
															return seriesName;
														}

										},
										markers: {
											width: 0
										}
	
      								}
		}

		var chart = new ApexCharts(document.querySelector("#chart-candlestick"), options);

		chart.render();

		chart.zoomX(
            <?=$start_time*1000?>,
			<?=$end_time*1000?>,
        )

     var optionsBar = {
					    series: [{
						          name: 'volume',
						          data: <?=$volume_chart?>
						        }],
				        chart: {
				          height: 160,
				          type: 'bar',
				          brush: {
				            enabled: true,
				            target: 'candles'
				          },
				          zoom: {
				            enabled: false
				          },
				          selection: {
				            enabled: false,
				            // xaxis: {
				            //   min: <?=$start_time*1000?>,
				            //   max: <?=$end_time*1000?>
				            // },
				            fill: {
				              color: '#ccc',
				              opacity: 0.4
				            },
				            stroke: {
				              color: '#0D47A1',
				            }
				          },
				        },
				        dataLabels: {
				          enabled: false
				        },
				        plotOptions: {
				          bar: {
				            columnWidth: '50%',
				            colors: {
				              ranges: [{
				                from: -1000,
				                to: 0,
				                color: '#ba4944'
				              }, {
				                from: 1,
				                to: 10000,
				                color: '#0dbb4f'
				              }]
				        
				            }
				          }
				        },
				        stroke: {
				          width: 0
				        },
				        xaxis: {
				          type: 'datetime',
				          // axisBorder: {
				          //   offsetX: -10
				          // }

				        },
				        yaxis: {
				          labels: {
				            show: true
				          },
				          tooltip: {
						                  enabled: true
						      			},
						    labels: {
										    formatter: function (value) {
										      return value.toFixed(2);
										    }
										}
				        }
				      };

        var chartBar = new ApexCharts(document.querySelector("#chart-bar"), optionsBar);
        chartBar.render();

        chartBar.zoomX(
            <?=$start_time*1000?>,
			<?=$end_time*1000?>,
        )

	</script>	

	<script type="text/javascript">

		var socket_url = 'wss://ws.infinite-market.io'; 

        $(function () {
           init();
         });

        function init()
        {
          websocket = new WebSocket(socket_url);
          websocket.onopen = function(evt) { onOpen(evt) };
          websocket.onclose = function(evt) { onClose(evt) };
          websocket.onmessage = function(evt) { onMessage(evt) };
          websocket.onerror = function(evt) { onError(evt) };

        }

         function onOpen(evt)
        {
          console.log("CONNECTED");
          //doSend('{"method":"server.time","params":"","id":""}');
          //doSend('{"method": "server.auth", "params": ["lfMqhYfwrElkGDGqR8quamZsO3uUFR","webv1.5"], "id": 153}');
          //doSend('{"method": "server.time", "params": [],"id": 156}');
          // doSend('{"method": "kline.query", "params": ["BTC-USDT",1602669600,1602673200,300], "id": 156}');          
          //doSend('{"method": "kline.subscribe", "params": ["BTC-USDT",10], "id": 157}');
          //doSend('{"method": "kline.update", "params": [], "id": 158}');
          //doSend('{"method": "asset.query", "params": ["BTC"], "id": 154}');

          doSend('{"method": "kline.query", "params": ["<?=$market?>",<?=$start_time_minus?>,<?=$end_time?>,<?=$interval?>], "id": 156}');

          // doSend('{"method": "kline.query", "params": ["<?=$market?>",<?=$start_time?>,<?=$end_time?>,60*5], "id": 101}');
          // doSend('{"method": "kline.query", "params": ["<?=$market?>",<?=$start_time?>,<?=$end_time?>,60*10], "id": 102}');
          // doSend('{"method": "kline.query", "params": ["<?=$market?>",<?=$start_time?>,<?=$end_time?>,60*30], "id": 103}');

          doSend('{"method": "kline.subscribe", "params": ["<?=$market?>",<?=$subscribe_interval?>], "id": 158}');
        }

        function onClose(evt)
        {
          console.log("DISCONNECTED");
          init();
        }

        function onMessage(evt)
        {
        	console.log("came");
        	console.log(evt.data);
 				
            var response = evt.data;

            response = $.parseJSON(response);

	  	    if (response.result != undefined && $.isArray(response.result) ) {

	  	    	var id = response.id;
	  	 		//replaceData(response.result,id);
	  	 		updateData(response.result,id,true);
	  	 	} else if (response.params != undefined && $.isArray(response.params)) {  

	  	 	    var id = response.id;           
	  	 		//appendData(response.params,id);
	  	 		updateData(response.result,id,false);
	  	 	}

        }

        function onError(evt)
        {
        	console.log("ERROR");
          //writeToScreen('<span style="color: red;">ERROR:</span> ' + evt.data);
        }

        function doSend(message)
        {
          //writeToScreen("SENT: " + message);
          websocket.send(message);
        }

        function writeToScreen(message)
        {

          $("#interactive").append(message+'<br/>');
        }

        function updateData(result,id,replace=true) {

        	  var line_1_periods = <?=$line_1_periods?>;
        	  var line_2_periods = <?=$line_2_periods?>;
        	  var line_3_periods = <?=$line_3_periods?>;

          	  var candle_stick_data = [];
          	  var bar_data = [];
          	  var candle_line_data = [];
          	  var candle_line_data_2 = [];
          	  var candle_line_data_3 = [];
          	  var end_time = 0;

          	  var prev_price = 0;
          	  var i = 0;

          	  $.each(result,function(key,row){
          	  	  /*var date = new Date(row[0]*1000);
          	  	  var new_date = date.getHours() +':'+date.getMinutes();*/

          	  	  var time = row[0];
          	  	  var o = row[1];
          	  	  var c = row[2];
          	  	  var h = row[3];
          	  	  var l = row[4];
          	  	  var vol = row[5];

          	  	  i = i+1;

          	  	  end_time = time*1000;

          	  	  var new_row = {
          	  	  					x : time*1000,
          	  	  					y : [o,h,l,c]
          	  	  				};

          	  	  candle_stick_data.push(new_row); 

          	  	  bar_data.push({
          	  	  					x : time*1000,
          	  	  					y : vol
          	  	  				});  

          	  	  prev_price = prev_price + (c*1.0); 
          
          	  	  var price_avg = prev_price/i; 
          	  	   //price_avg = parseInt(price_avg*10000000)/10000000;

          	  	  //line 1 (5mins)
          	  	  if (i == 1 || i%line_1_periods==0){

	          	  	  candle_line_data.push({
	          	  	  					x : time*1000,
	          	  	  					y : price_avg
	          	  	  				});
	          	  }


          	  	  //line 2 (10mins)
          	  	  if (i == 1 || i%line_2_periods==0){
          	  	  	candle_line_data_2.push({
          	  	  					x : time*1000,
          	  	  					y : price_avg
          	  	  				});
          	  	  }

          	  	  //line 2 (10mins)
          	  	  if (i == 1 || i%line_3_periods==0){
          	  	  	candle_line_data_3.push({
          	  	  					x : time*1000,
          	  	  					y : price_avg
          	  	  				});
          	  	  }
          	  });

          	  if (replace == true){

	          	  chart.updateSeries([{
							            name: 'line',
							            data: candle_line_data
							          },{
							            name: 'line_2',
							            data: candle_line_data_2
							          },{
							            name: 'line_3',
							            data: candle_line_data_3
							          },{
							            name: 'price',
							            data: candle_stick_data
							          }
							          ]);

	          	  chartBar.updateSeries([{
							            name: 'volume',
							            data: bar_data
							          }]);
	          	} else {
	          		chart.appendData([{
							            name: 'line',
							            data: candle_line_data
							          },{
							            name: 'line_2',
							            data: candle_line_data_2
							          },{
							            name: 'line_3',
							            data: candle_line_data_3
							          },{
							            name: 'price',
							            data: candle_stick_data
							          }
							          ]);

	          	  chartBar.appendData([{
							            name: 'volume',
							            data: bar_data
							          }]);
	          	}

        }

         function appendData(result,id) {

          	  var candle_stick_data = [];
          	  var bar_data = [];
          	  var candle_line_data = [];
          	  var end_time = 0;

          	  var prev_price = 0;
          	  var i = 0;

          	  $.each(result,function(key,row){
          	  	  /*var date = new Date(row[0]*1000);
          	  	  var new_date = date.getHours() +':'+date.getMinutes();*/

          	  	  var time = row[0];
          	  	  var o = row[1];
          	  	  var c = row[2];
          	  	  var h = row[3];
          	  	  var l = row[4];
          	  	  var vol = row[5];

          	  	  i = i+1;

          	  	  end_time = time*1000;

          	  	  var new_row = {
          	  	  					x : time*1000,
          	  	  					y : [o,h,l,c]
          	  	  				};

          	  	  candle_stick_data.push(new_row); 

          	  	  bar_data.push({
          	  	  					x : time*1000,
          	  	  					y : vol
          	  	  				});  

          	  	  prev_price = prev_price + (c*1.0); 
          
          	  	  var price_avg = prev_price/i; 

          	  	  candle_line_data.push({
          	  	  					x : time*1000,
          	  	  					y : price_avg
          	  	  				});
          	  });


          	   chart.appendData([{
						            name: 'line',
						            data: candle_line_data
						          },{
						            name: 'price',
						            data: candle_stick_data
						          }
						          ]);

          	  chartBar.appendData([{
						            name: 'volume',
						            data: bar_data
						          }]);

        }

	</script>
</body>
</html>   