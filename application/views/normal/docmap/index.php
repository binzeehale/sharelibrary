<style type="text/css">
  #baidu-map {
    float: left;
    width: 740px;
    height:500px;
    border: 1px solid #ddd;
  }

  .marker-title {
    float: left;
    padding:8px;
    width: 18px;
    border: 1px solid #ddd;
/*    border-top:1px solid #ddd;
    border-right:1px solid #ddd;
    border-left:1px solid #ddd;*/
    background-color: #fff;
    z-index: 2;
  }
  .marker-list{
    padding:8px;
    border:1px solid #ddd;
    background-color: #fff;
    word-break: break-all;
    max-height: 500px;
    overflow-y: auto;
    -ms-overflow-y:; auto;
    z-index: 1;
  }

  .marker-list ul{
    height:800px;
  }
  .table i {
    margin-top: 4px;
  }

  strong {
    word-break: break-all;
  }
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<div class="container">
  <div>
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="<?=base_url('/docmap/index')?>">百度地图</a>
      </li>
      <li>
        <a href="<?=base_url('/docmap/google')?>">谷歌地图</a>
      </li>
    </ul>
    <div class="row-fluid">
      <div class="marker-list span2">
          <ul>
          <?php foreach($marks as $mark):?>
          <li>
            <a href="javascript:showWindow(<?=$mark['id']?>)"><?=$mark['name']?></a>
          </li>
          <?php endforeach;?>
          </ul>
      </div>
      <div class="marker-title">
         <a href="javascript:slideToggle()">标记列表</a>
      </div>
      <div id="baidu-map" class="span9"></div>
    </div> 
  </div>
</div>  
<script type="text/javascript"> 

  function slideToggle(){

    if($('.marker-list').is(":visible")){
        $('.marker-list').hide("slide",function(){
          $('#baidu-map').width(880);
        });
        
    }else{
        $('.marker-list').show("slide");
        $('#baidu-map').width(740);
    }
  }

  if (window.innerHeight)
    winHeight = window.innerHeight;
  else if ((document.body) && (document.body.clientHeight))
    winHeight = document.body.clientHeight;
  winHeight -= 140;
  $('#baidu-map').height( winHeight > 500 ? winHeight : 500);

  /*infoWindow Template*/
  var newWindowTpl = [
    '<form id="$id$" action="<?=base_url('/docmap/createMarker')?>" method="POST">',
      '<div class="control-group">',
        '<label class="control-label">坐标</label>',
        '<div class="controls">',
          '<p>经度:$lng$</p><p>纬度:$lat$</p>',
          '<input type="hidden" name="lng" value="$lng$" />',
          '<input type="hidden" name="lat" value="$lat$" />',
          '<input type="hidden" name="origin" value="baidu" />',
        '</div>',
      '</div>',
      '<div class="control-group">',
        '<label class="control-label">名称</label>',
        '<div class="controls">',
          '<input name="markerName" type="text" value="">',
        '</div>',
      '</div>',
      '<div class="control-group">',
        '<label class="control-label"></label>',
        '<div class="controls">',
          '<button class="btn btn-success">保存</button>',
        '</div>',
      '</div>',
    "</form>"
  ].join('');

  var fileWindowTpl = [
      '<div>',
        '<p style="margin-top:20px;">',
          '<strong>$mark-name$</strong>',
          '<a class="pull-right" href="javascript:deleteMarker($mark-id$)">',
            '<i class="icon-trash"></i>',
          '</a>',
        '</p>',
        '<p>经度:$lng$</p><p>纬度:$lat$</p>',
      '</div>',
      '<table class="table table-hover">',
        '<thead>',
          '<tr>',
            '<th>名称</th>',
            '<th>最近修改时间</th>',
        '</thead>',
        '<tbody>',
          '$files$',
        '</tbody>',
      '</table>'
  ].join('');

  function uniqueId(){
    return 'marker_' + (new Date()).getTime();
  }

  function getMarkerIndexById(id){

      for ( var i in markers ){
        if(markers[i].id == id){
          return i;
        }
      }
      return -1;
  }

  /* marker validater options */
  function setValidateOptions( markerFunc ){

    var validateOptions = {
      rules:{
        markerName: {
          required: true,
          legalStringCN: true,
          maxlength: 20,
          remote: {
            url: "<?=base_url('/docmap/validateMarker')?>",
            type: "post",
            data: {
                markerName : markerFunc || null
              }
            }
          }
        },
        messages:{
          markerName:{
            remote: "标记名称已使用"
          }
        }
    };
    return validateOptions;
  }

  function createNewWindow(p){
    
    var marker = new BMap.Marker(p);
    map.addOverlay(marker);

    var markerId = uniqueId();
    var tpl = newWindowTpl.replace(/\$id\$/, markerId )
                          .replace(/\$lng\$/g,p.lng)
                          .replace(/\$lat\$/g,p.lat);

    var options = { width: 250 , height : 250 }
    var infoWindow = new BMap.InfoWindow(tpl,options);
    marker.addEventListener("click", function(){
      this.openInfoWindow(infoWindow);
    });

    infoWindow.addEventListener("close",function(){
      map.removeOverlay(marker);
      //var index = getMarkerIndexById(markerId);
    });
    marker.openInfoWindow(infoWindow);
    var markerName = function(){
      return $('#'+markerId).find('[name="markerName"]').val();
    }
    var timer = setInterval(function(){
      if($('#'+markerId).get(0)){
        clearInterval(timer);
        $('#'+markerId).validate(setValidateOptions(markerName));
      }
    },100);
  }

  function showWindow( mId ){
    
    var index = getMarkerIndexById(mId);
    if(index < 0 ){
      alert('error! Invalid mark id ');
    }else{
      var marker = markers[index].marker;
      marker.openInfoWindow(markers[index].infoWindow);
    }
  }

  function loadMarker( m ){

    var marker = new BMap.Marker(new BMap.Point(parseFloat(m.lng),parseFloat(m.lat)));
    map.addOverlay(marker);

    var options = { width: 0 , height : 0 };
    var tpl = [
      '<tr>',
        '<td><a href="<?=base_url('/doclib/paper')?>/$id$"><i class="icon-file"></i>$name$</td>',
        '<td>$time$</td>',
      '</tr>'
    ].join('');

    var folderTpl = [
    '<tr>',
        '<td><a href="<?=base_url('/doclib/index')?>/$id$"><i class="icon-folder-close"></i>$name$</td>',
        '<td>$time$</td>',
      '</tr>'
    ].join('');

    var fileHtml = '';
    for(var i in m.docs){
      var f = m.docs[i];
      if(f.type == <?=ZB_FILE?>){
      fileHtml += tpl.replace(/\$id\$/,f.id)
                      .replace(/\$name\$/,f.name)
                      .replace(/\$time\$/,f.last_update_time);
      }else{
      fileHtml += folderTpl.replace(/\$id\$/,f.id)
                      .replace(/\$name\$/,f.name)
                      .replace(/\$time\$/,f.last_update_time);
      }
    }
    fileHtml = fileHtml == ""?'<tr><td colspan="2"><center>无文件</center></td></tr>':fileHtml;
    var fileWindowHtml = fileWindowTpl.replace(/\$files\$/,fileHtml)
                                      .replace(/\$lat\$/,m.lat)
                                      .replace(/\$lng\$/,m.lng)
                                      .replace(/\$mark-id\$/,m.id)
                                      .replace(/\$mark-name\$/,m.name);
    var infoWindow = new BMap.InfoWindow(fileWindowHtml,options);
    marker.addEventListener("click", function(){
      this.openInfoWindow(infoWindow);
    });

    return { id : parseInt(m.id) , marker: marker , infoWindow : infoWindow };
  }

  function deleteMarker(id){

    bootbox.confirm('确认删除该标记吗？（此操作会将该标记下的文件标记设为空）',function(result){
      if(result){
        $.post('<?=base_url("/docmap/deleteMarker")."/"?>'+id, function(response){
          if(response.action == 'success'){
            window.location.reload();
          }else{
            alert(response.data);
          }
        },'json');
      }
    });
  }

  /* init baidu map */
  var map = new BMap.Map("baidu-map",{mapType: BMAP_HYBRID_MAP});
  var pzhPosition =  { lng: 101.725154 , lat: 26.584211 }
  var point = new BMap.Point( pzhPosition.lng , pzhPosition.lat );
  map.centerAndZoom(point, 15);

  map.addControl(new BMap.NavigationControl());
  map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_HYBRID_MAP]})); 


  var contextMenu = new BMap.ContextMenu();
  var txtMenuItem = [
    {
     text:'放大',
     callback:function(){map.zoomIn()}
    },
    {
     text:'缩小',
     callback:function(){map.zoomOut()}
    },
    {
     text:'放置到最大级',
     callback:function(){map.setZoom(18)}
    },
    {
     text:'查看全国',
     callback:function(){map.setZoom(4)}
    },
    {
     text:'在此添加标注',
     callback: createNewWindow
    }
   ];
   for(var i=0; i < txtMenuItem.length; i++){
    contextMenu.addItem(new BMap.MenuItem(txtMenuItem[i].text,txtMenuItem[i].callback,100));
    if(i==1 || i==3) {
     contextMenu.addSeparator();
    }
   }
   map.addContextMenu(contextMenu);
   /* end init baidu map */

   /* init markers */
  var marks = <?=json_encode($marks)?>;
  var markers = [];
  for(var i in marks){
    markers.push(loadMarker(marks[i]));
  }
  /* end init markers */
</script>