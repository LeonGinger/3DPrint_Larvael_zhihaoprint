<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <button style="margin-right: 10px;" data-start="{{ \Carbon\Carbon::now()->subDays(7)->toDateString() }}" data-end="{{ \Carbon\Carbon::today()->toDateString() }}" class="date-range btn btn-xs btn-primary">最近7日</button>
                <button style="margin-right: 10px;" data-start="{{ \Carbon\Carbon::now()->subDays(30)->toDateString() }}" data-end="{{ \Carbon\Carbon::today()->toDateString() }}" class="date-range btn btn-xs btn-default">最近30日</button>
                {{--<button style="margin-right: 10px;" data-start="{{ \Carbon\Carbon::now()->subDays(90)->toDateString() }}" data-end="{{ \Carbon\Carbon::today()->toDateString() }}" class="date-range btn btn-xs btn-default">最近90日</button>--}}
                {{--<div class="date" style="display: inline-block;">--}}
                    {{--<input type="text" id="date_range" placeholder="点击筛选日期" style="outline: none;width: 200px;">--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div id="contain" style="width: 100%;height:400px;"></div>
    </div>
</div>
<script type="text/javascript">
  //日期范围
  laydate.render({
    elem: '#date_range'
    , range: true
  });
  $(function () {
    initChart();
    function initChart() {
      var start_date = "{{ \Carbon\Carbon::now()->subDays(7)->toDateString() }}";
      var today = "{{ \Carbon\Carbon::now()->toDateString() }}";
      var names = [];
      var ttls = [];

      $.get("{{ url('/console/tenant_data') }}", {
        "_token": "{{ csrf_token() }}",
        "start_date": start_date,
        "end_date": today
      }, function (response) {
        var dataArr = JSON.parse(response)
        dataArr.forEach(function(item) {
          names.push(item.name);
          ttls.push(item.value);
        })
      });

      setTimeout(function() {
        var ctx = echarts.init(document.getElementById("contain"));
        var option = {
          title: {
            text: '新增商户数'
          },
          tooltip: {
            trigger: 'axis'
          },
          legend: {
            data: ['数据大小']
          },
          toolbox: {
            show: true,
            feature: {
              mark: {show: true},
              dataView: {show: true, readOnly: false},
              magicType: {show: true, type: ['line', 'bar']},
              restore: {show: true},
              saveAsImage: {show: true}
            }
          },
          calculable: true,
          xAxis: [
            {
              axisLine: {
                lineStyle: {color: '#333'}
              },
              axisLabel: {
                rotate: 30,
                interval: 0
              },
              type: 'category',
              boundaryGap: false,
              data: names    // x的数据，为上个方法中得到的names
            }
          ],
          yAxis: [
            {
              type: 'value',
              min: 0,
              max: 30,
              axisLabel: {
                formatter: '{value}'
              },
              axisLine: {
                lineStyle: {color: '#333'}
              }
            }
          ],
          series: [
            {
              name: '',
              type: 'line',
              smooth: 0.3,
              data: ttls   // y轴的数据，由上个方法中得到的ttls
            }
          ]
        };
        ctx.setOption(option)
      }, 1000)
    }
  })
</script>
<script type="text/javascript">
  $(function() {
    var dateRangeBtn = $('.date-range');
    dateRangeBtn.click(function() {
      var that = $(this);
      if (!that.hasClass('btn-primary')) {
        that.removeClass('btn-default')
          .addClass('btn-primary')
          .siblings('.date-range')
          .removeClass('btn-primary')
          .addClass('btn-default')
      }
      var startDate = that.data('start');
      var endDate = that.data('end');
      var names = [];
      var ttls = [];
      $.get("{{ url('/console/tenant_data') }}", {
        "_token": "{{ csrf_token() }}",
        "start_date": startDate,
        "end_date": endDate
      }, function (response) {
        var dataArr = JSON.parse(response)
        dataArr.forEach(function(item) {
          names.push(item.name);
          ttls.push(item.value);
        })
      });
      setTimeout(function() {
        var ctx = echarts.init(document.getElementById("contain"));
        var option = {
          title: {
            text: '新增商户数'
          },
          tooltip: {
            trigger: 'axis'
          },
          legend: {
            data: ['数据大小']
          },
          toolbox: {
            show: true,
            feature: {
              mark: {show: true},
              dataView: {show: true, readOnly: false},
              magicType: {show: true, type: ['line', 'bar']},
              restore: {show: true},
              saveAsImage: {show: true}
            }
          },
          calculable: true,
          xAxis: [
            {
              axisLine: {
                lineStyle: {color: '#333'}
              },
              axisLabel: {
                rotate: 30,
                interval: 0
              },
              type: 'category',
              boundaryGap: false,
              data: names    // x的数据，为上个方法中得到的names
            }
          ],
          yAxis: [
            {
              type: 'value',
              min: 0,
              max: 30,
              axisLabel: {
                formatter: '{value}'
              },
              axisLine: {
                lineStyle: {color: '#333'}
              }
            }
          ],
          series: [
            {
              name: '',
              type: 'line',
              smooth: 0.3,
              data: ttls   // y轴的数据，由上个方法中得到的ttls
            }
          ]
        };
        ctx.setOption(option)
      }, 1000)
    });
  })
</script>