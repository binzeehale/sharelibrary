<style type="text/css">
  #map-canvas {
    height: 500px;
    border: 1px solid #ddd;
  }
  
  #map-canvas {
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
<div class="container">
  <div>
    <ul class="nav nav-tabs">
      <li>
        <a href="<?=base_url('/docmap/index')?>">百度地图</a>
      </li>
      <li  class="active">
        <a href="<?=base_url('/docmap/google')?>">谷歌地图</a>
      </li>
    </ul>
	 
	<div class="row-fluid">
      <div class="span2 marker-list" style="">
          <h4>标记名称</h4>
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
      <div id="map-canvas" class="span9"></div>
    </div>
  </div>
</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>
function slideToggle(){

  if($('.marker-list').is(":visible")){
      $('.marker-list').hide("slide",function(){
        $('#map-canvas').width(880);
      });
      
  }else{
      $('.marker-list').show("slide");
      $('#map-canvas').width(740);
  }
}

if (window.innerHeight)
  winHeight = window.innerHeight;
else if ((document.body) && (document.body.clientHeight))
  winHeight = document.body.clientHeight;
winHeight -= 140;
$('#map-canvas').height( winHeight > 500 ? winHeight : 500);

 var newWindowTpl = [
    '<form style="margin-left:15px;" id="$id$" action="<?=base_url('/docmap/createMarker')?>" method="POST">',
      '<div class="control-group">',
        '<label class="control-label">坐标</label>',
        '<div class="controls">',
          '<p>经度:$lng$</p><p>纬度:$lat$</p>',
          '<input type="hidden" name="lng" value="$lng$" />',
          '<input type="hidden" name="lat" value="$lat$" />',
          '<input type="hidden" name="origin" value="google" />',
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
		'<th>文件名称</th>',
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
	if(t_markers[i].id == id){
	  return i;
	}
  }
  return -1;
}

function creatMarker(){
	placeMarker(clickedLatLng);
}

function loadMarker(m){

	var location =  new google.maps.LatLng(m.lat,m.lng); 
    var marker = new google.maps.Marker({
        position: location, 
        map: map,
		title: m.name
    });
	
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
	marker.infowindow = new google.maps.InfoWindow({ 
		content: fileWindowHtml,//"<div class='my_marker'><p id='title'>"+title+"</p><p><b>地址:</b>"+ address +"</p><a class='fileDetail' style='text-decoration:none;color:#2679BA;float:right' href='javascript:openFileWindow(\""+title+"\");'>文件列表>></a></div>",
		//size: new google.maps.Size(150,100)
		maxWidth: 400
	});
	
	google.maps.event.addListener(marker, 'click', function() {
		marker.infowindow.open(map,marker);	
	});
	google.maps.event.addListener(marker, 'mouseover', function() {
		//markers[0].infowindow.close();
		closeInfoWindows();
		marker.infowindow.open(map,marker);
	});

	markers.push(marker);
	closeInfoWindows();
	//marker.infowindow.open(map,marker);
    //map.setCenter(location);
	
	return { id : parseInt(m.id) , marker: marker };
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

function placeMarker( m ) {
	var location =  new google.maps.LatLng(m.lat(),m.lng());
    var marker = new google.maps.Marker({
        position: location, 
        map: map,
		title:"Hello"
    });
	
	var markerId = uniqueId();
    var tpl = newWindowTpl.replace(/\$id\$/, markerId )
                          .replace(/\$lng\$/g,m.lng())
                          .replace(/\$lat\$/g,m.lat()); 
	
	marker.infowindow = new google.maps.InfoWindow({ 
		content: tpl,
		//size: new google.maps.Size(50,50)
		maxWidth: 400
	});
	
	google.maps.event.addListener(marker, 'click', function() {
		marker.infowindow.open(map,marker);
		
	});
	google.maps.event.addListener(marker, 'mouseover', function() {
		//markers[0].infowindow.close();
		closeInfoWindows();
		marker.infowindow.open(map,marker);
	});

	markers.push(marker);
	closeInfoWindows();
	marker.infowindow.open(map,marker);
    map.setCenter(location);
}

function closeInfoWindows()
{
	for(num in markers){
		markers[num].infowindow.close();
	}
}

function showWindow( mId ){

	var index = getMarkerIndexById(mId);
	if(index < 0 ){
		alert('error! Invalid mark id ');
	}else{
		var marker = t_markers[index].marker;
		closeInfoWindows();
		marker.infowindow.open(map,marker);
		//map.setCenter(location);
	}
}

function createContextMenu(controlUI,map) {
        contextmenu = document.createElement("div");
        contextmenu.style.display = "none";
        contextmenu.style.background = "#ffffff";
        contextmenu.style.border = "1px solid #8888FF";
        contextmenu.innerHTML = 
        "<a href='javascript:zoomIn()'><div class='context'> 放大 </div></a>"
        + "<a href='javascript:zoomOut()'><div class='context'> 缩小 </div></a>"
		+ "<a href='javascript:creatMarker()'><div class='context'> 以此创建标记 </div></a>";
        controlUI.appendChild(contextmenu);
        
        google.maps.event.addDomListener(map, 'rightclick', function (event) {
		
				clickedLatLng = event.latLng;
				
                contextmenu.style.position="relative";
                contextmenu.style.left=(event.pixel.x-80)+"px";        //平移显示以对应右键点击坐标
                contextmenu.style.top=event.pixel.y+"px";
                contextmenu.style.display = "block";
        });
        
        google.maps.event.addDomListener(controlUI, 'click', function () {
                contextmenu.style.display = "none";
        });
        
        google.maps.event.addDomListener(map, 'click', function () {
                contextmenu.style.display = "none";
        });
        google.maps.event.addDomListener(map, 'drag', function () {
                contextmenu.style.display = "none";
        });
}

function zoomIn()   
{  
    map.setZoom(map.getZoom() + 1); 
}     
     
function zoomOut()   
{  
   map.setZoom(map.getZoom() - 1); 
}
    
function zoomInHere()   
{  
    var point =  new google.maps.LatLng(clickedLatLng.Ua , clickedLatLng.Va);
   map.setCenter(point,map.getZoom()+1);   
}
      
function zoomOutHere()   
{  
    var point = map.fromContainerPixelToLatLng(clickedLatLng)  
    map.setCenter(point,map.getZoom()-1);    
}    
      
function centreMapHere()   
{  
    var point = map.fromContainerPixelToLatLng(clickedLatLng)  
    map.setCenter(point);  
}

/*create map */
var map = null;
var markers = [];

function initialize() {
  var myLatlng = new google.maps.LatLng(26.586305,101.722274);
  var myOptions = {
    zoom: 14,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.SATELLITE
  }
  map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
  
  //placeMarker(myLatlng);
	var ContextMenuControlDiv = document.createElement('DIV');
	var ContextMenuControl = new createContextMenu(ContextMenuControlDiv, map);

	ContextMenuControlDiv.index = 1;
	/*增加层的方式*/
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(ContextMenuControlDiv);
}
/*end create map */

var clickedLatLng;

var marks = <?=json_encode($marks)?>;
var t_markers = [];

initialize();
for(var i in marks){
	t_markers.push(loadMarker(marks[i]));
}
  
//google.maps.event.addDomListener(window, 'load', initialize);
</script>
