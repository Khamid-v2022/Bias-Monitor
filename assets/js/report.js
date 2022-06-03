$(function() { 
    $('#bias_master_data').DataTable({
        "pageLength": 100
    }); 
    
    if(url_datas.length > 0){
        $(".no-data").css({display: "none"});
    	var pie_basic_element = document.getElementById('pie_basic');
    	var pie_basic = echarts.init(pie_basic_element);
        pie_basic.setOption({

            // Colors
            color: [
                '#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80',
                '#8d98b3','#e5cf0d','#97b552','#95706d','#dc69aa',
                '#07a2a4','#9a7fd1','#588dd5','#f5994e','#c05050',
                '#59678c','#c9ab00','#7eb00a','#6f5553','#c14089'
            ],

            // Global text styles
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },

            // Add title
            // title: {
            //     text: 'Browser popularity',
            //     subtext: 'Open source information',
            //     left: 'center',
            //     textStyle: {
            //         fontSize: 17,
            //         fontWeight: 500
            //     },
            //     subtextStyle: {
            //         fontSize: 12
            //     }
            // },

            // Add tooltip
            tooltip: {
                trigger: 'item',
                backgroundColor: 'rgba(0,0,0,0.75)',
                padding: [10, 15],
                textStyle: {
                    fontSize: 13,
                    fontFamily: 'Roboto, sans-serif'
                },
                // formatter: "{a} <br/>{b}: {c} ({d}%)"
            },

            // Add legend
            legend: {
                orient: 'vertical',
                top: 'center',
                // top: 300,
                left: 400,
                data: legend_data,
                itemHeight: 8,
                itemWidth: 8
            },

            // Add series
            series: [{
                name: 'URL',
                type: 'pie',
                radius: '70%',
                center: ['200px', '57.5%'],
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff',
                        label: {
                            show: false
                        },
                        labelLine: {
                            show: false
                        }
                    }
                },
                data: url_datas
            }]
        });
    }else{
        $("#pie_basic").css({display: "none"});
        $("#pie_basic").next().css({display: "block"});
    }

    // bar-chart
    if(topic_data[0]){
        $(".no-data").css({display: "none"});
        var columns_basic_element = document.getElementById('column_basic');
        var columns_basic = echarts.init(columns_basic_element);
        
        columns_basic.setOption({

            // Define colors
            color: ['#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80'],
            // Global text styles
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },

            // Chart animation duration
            animationDuration: 750,

            // Setup grid
            grid: {
                left: 0,
                right: 40,
                top: 35,
                bottom: 0,
                containLabel: true
            },

            // Add tooltip
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(0,0,0,0.75)',
                padding: [10, 15],
                textStyle: {
                    fontSize: 13,
                    fontFamily: 'Roboto, sans-serif'
                }
            },

            // Horizontal axis
            xAxis: [{
                type: 'category',
                data: topics,
                axisLabel: {
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                }
            }],

            // Vertical axis
            yAxis: [{
                type: 'value',
                axisLabel: {
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                }
            }],

            // Add series
            series: [
                {
                    type: 'bar',
                    data: topic_data,
                    itemStyle: {
                        normal: {
                            label: {
                                show: true,
                                position: 'top',
                                textStyle: {
                                    fontWeight: 500
                                }
                            }
                        }
                    }
                }
            ]
        });
    }else{
        $("#column_basic").css({display: "none"});
         $("#column_basic").next().css({display: "block"});
    }

    var series_1 =[];
    if(exist_topic2){
        series_1 = [
            {
                name: topics[0],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias1
            },
            {
                name: topics[1],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias2
            },
            {
                name: topics[2],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_neutral
            }
        ];
    }else{
        series_1 = [
            {
                name: topics[0],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias1
            },
            {
                name: topics[2],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_neutral
            }
        ];

    }

    if(histogram_dates.length > 0){
        $(".no-data").css({display: "none"});
        var line_zoom_element = document.getElementById('line_zoom');
        var line_zoom = echarts.init(line_zoom_element);

        var zoom_show_start_percent = 0;

        // if length more than one month days
        if(histogram_dates.length > 30){
            zoom_show_start_percent = 100 - 30 / histogram_dates.length * 100;
        }

        line_zoom.setOption({

            // Define colors
            color: ["#424956", "#d74e67", '#0092ff'],

            // Global text styles
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },

            // Chart animation duration
            animationDuration: 750,

            // Setup grid
            grid: {
                left: 0,
                right: 40,
                top: 35,
                bottom: 60,
                containLabel: true
            },

            // Add legend
            legend: {
                data: topics,
                itemHeight: 8,
                itemGap: 20
            },

            // Add tooltip
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(0,0,0,0.75)',
                padding: [10, 15],
                textStyle: {
                    fontSize: 13,
                    fontFamily: 'Roboto, sans-serif'
                }
            },

            // Horizontal axis
            xAxis: [{
                type: 'category',
                boundaryGap: false,
                axisLabel: {
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                },
                data: histogram_dates
                // data: ['2017/1/17','2017/1/18','2017/1/19','2017/1/20','2017/1/23','2017/1/24','2017/1/25','2017/1/26','2017/2/3','2017/2/6','2017/2/7','2017/2/8','2017/2/9','2017/2/10','2017/2/13','2017/2/14','2017/2/15','2017/2/16','2017/2/17','2017/2/20','2017/2/21','2017/2/22','2017/2/23','2017/2/24','2017/2/27','2017/2/28','2017/3/1分红40万','2017/3/2','2017/3/3','2017/3/6','2017/3/7']

            }],

            // Vertical axis
            yAxis: [{
                type: 'value',
                axisLabel: {
                    formatter: '{value} ',
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                    }
                }
            }],

            // Zoom control
            dataZoom: [
                {
                    type: 'inside',
                    start: zoom_show_start_percent,
                    end: 100
                },
                {
                    show: true,
                    type: 'slider',
                    start: 30,
                    end: 70,
                    height: 40,
                    bottom: 0,
                    borderColor: '#ccc',
                    fillerColor: 'rgba(0,0,0,0.05)',
                    handleStyle: {
                        color: '#585f63'
                    }
                }
            ],

            // Add series
            series: series_1
        });
    }else{
        $("#line_zoom").css({display: "none"});
        $("#line_zoom").next().css({display: "block"});
    }

    var series_2 = [
            {
                name: score_legend[0],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias1_neg
            },
            {
                name: score_legend[1],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias1_neu
            },
            {
                name: score_legend[2],
                type: 'line',
                smooth: true,
                symbolSize: 6,
                itemStyle: {
                    normal: {
                        borderWidth: 2
                    }
                },
                data: histogram_bias1_pos
            }
        ];
    // if(exist_topic2){
    //     series_2 = [
    //         {
    //             name: score_legend[0],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_neg
    //         },
    //         {
    //             name: score_legend[1],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_neu
    //         },
    //         {
    //             name: score_legend[2],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_pos
    //         },
    //         {
    //             name: score_legend[3],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias2_neg
    //         },
    //         {
    //             name: score_legend[4],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias2_neu
    //         },
    //         {
    //             name: score_legend[5],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias2_pos
    //         }
    //     ]
    // }else{
    //     series_2 = [
    //         {
    //             name: score_legend[0],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_neg
    //         },
    //         {
    //             name: score_legend[1],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_neu
    //         },
    //         {
    //             name: score_legend[2],
    //             type: 'line',
    //             smooth: true,
    //             symbolSize: 6,
    //             itemStyle: {
    //                 normal: {
    //                     borderWidth: 2
    //                 }
    //             },
    //             data: histogram_bias1_pos
    //         }
    //     ];
    // }

    if(score_histogram_dates.length > 0){
        $(".no-data").css({display: "none"});
        var score_element = document.getElementById('score_histogram');
        var score_zoom = echarts.init(score_element);

        var score_show_start_percent = 0;

        // if length more than one month days
        if(score_histogram_dates.length > 30){
            score_show_start_percent = 100 - 30 / score_histogram_dates.length * 100;
        }

        score_zoom.setOption({

            // Define colors            
            color: ["#d74e67", '#d74ed7', '#b04ed7', '#4e79d7', '#4ed2d7', '#4ed775'],

            // Global text styles
            textStyle: {
                fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                fontSize: 13
            },

            // Chart animation duration
            animationDuration: 750,

            // Setup grid
            grid: {
                left: 0,
                right: 40,
                top: 35,
                bottom: 60,
                containLabel: true
            },

            // Add legend
            legend: {
                data: score_legend,
                itemHeight: 8,
                itemGap: 20
            },

            // Add tooltip
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(0,0,0,0.75)',
                padding: [10, 15],
                textStyle: {
                    fontSize: 13,
                    fontFamily: 'Roboto, sans-serif'
                }
            },

            // Horizontal axis
            xAxis: [{
                type: 'category',
                boundaryGap: false,
                axisLabel: {
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                },
                data: score_histogram_dates
            }],

            // Vertical axis
            yAxis: [{
                type: 'value',
                axisLabel: {
                    formatter: '{value} ',
                    color: '#333'
                },
                axisLine: {
                    lineStyle: {
                        color: '#999'
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                    }
                }
            }],

            // Zoom control
            dataZoom: [
                {
                    type: 'inside',
                    start: score_show_start_percent,
                    end: 100
                },
                {
                    show: true,
                    type: 'slider',
                    start: 30,
                    end: 70,
                    height: 40,
                    bottom: 0,
                    borderColor: '#ccc',
                    fillerColor: 'rgba(0,0,0,0.05)',
                    handleStyle: {
                        color: '#585f63'
                    }
                }
            ],

            // Add series
            series: series_2
        });
    }else{
        $("#score_histogram").css({display: "none"});
        $("#score_histogram").next().css({display: "block"});
    }


    var triggerChartResize = function() {
        pie_basic_element && pie_basic.resize();
        columns_basic_element && columns_basic.resize();
        line_zoom_element && line_zoom.resize();
        score_element && score_zoom.resize();
    };


    $(document).on('click', '.sidebar-control', function() {
        setTimeout(function () {
            triggerChartResize();
        }, 0);
    });

    // On window resize
    var resizeCharts;
    window.onresize = function () {
        clearTimeout(resizeCharts);
        resizeCharts = setTimeout(function () {
            triggerChartResize();
        }, 200);
    };
})

