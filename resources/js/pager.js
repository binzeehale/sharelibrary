
//分页
var pager = {

  linkId : "pagination",
  tableId : "table",
  openCheck : { 'enable' : false },

  page : 1,
  pageNum : 10,
  showNum : 9,
  store: [],

  /**
    init();
    init({ linkId : 'pager' , tableId : 'table' });
    init({ linkId : 'pager' , tableId : 'table' , openCheck : { checkBoxClass:'pic-selection' , outsideArr:'picBox'} });
  **/

  init : function(){

    var args = Array.prototype.slice.call(arguments);
    if(args.length > 0){

      var setting = args[0];
      if(setting.hasOwnProperty('pageNum'))
        pager.pageNum = setting.pageNum;
      if(setting.hasOwnProperty('showNum'))
        pager.showNum = setting.showNum;
      if(setting.hasOwnProperty('linkId'))
        pager.linkId = setting.linkId;
      if(setting.hasOwnProperty('tableId'))
        pager.tableId = setting.tableId;
      if(setting.hasOwnProperty('openCheck')){
        pager.openCheck.enable = true;
        pager.openCheck.checkBoxClass = setting.openCheck.checkBoxClass;
        pager.openCheck.outsideArr = setting.openCheck.outsideArr;
      }

    }


    var trs = [];
    var thflag = 0;
    $('#' + pager.tableId ).find('tr').each(function(){
      if(thflag == 0){
        thflag++;
        return true;
      }
      trs.push('<tr>' + $(this).html() + '</tr>');
    });
    this.store = trs;
    this.page = 1;

    this.goto(this.page);
  },

  outputlink: function(){
    var total = this.store.length;

    var link = '<ul>';
    if(total <= pager.pageNum ) return '';

    var nums = Math.ceil(total/pager.pageNum);
    var start = 1;
    var end = nums;


    if( nums > pager.showNum ){
      if( pager.page <  Math.ceil(pager.showNum /2 )){
        start = 1;
        end = pager.showNum;
      }
      else if( pager.page + Math.ceil(pager.showNum /2 ) <= nums ){
        start = pager.page - Math.ceil(pager.showNum /2 ) + 1;
        end = pager.page + Math.ceil(pager.showNum /2 ) -1;
      }else{
        start = nums - pager.showNum + 1;
        end = nums;
      }
    } 
    
    link += '<li><a href="javascript:pager.goto(1)">&laquo;</a></li>';
    for ( var i = start ; i<= end ; i++){
      if(i == pager.page){
        link += '<li><a class="active" href="javascript:pager.goto('+ i +')">'+ i +'</a></li>';
      }else{
        link += '<li><a href="javascript:pager.goto('+ i +')">'+ i +'</a></li>';
      }
    }
    link += '<li><a href="javascript:pager.goto('+ nums + ')">&raquo;</a></li>';
    link += '</ul>';
    return link;
  },

  goto : function(index){
    this.page = index;
    var showtrs = this.store.slice((this.page -1 ) * this.pageNum , this.page * this.pageNum);
    var html = showtrs.join(' ');
    
    var tbody = $('#' + pager.tableId).find('tbody')[0];
    $(tbody).html(html);


    if(pager.openCheck.enable){
      if(window[pager.openCheck.outsideArr].length != 0)
      for( i in window[pager.openCheck.outsideArr]){
        $(tbody).find('tr').each(function(){

          var dom = $(this).find('.pic-selection')[0];

          if(dom.value == window[pager.openCheck.outsideArr][i].value){
            dom.checked = true;
          }
        });
      }
    }

    $('#' + pager.linkId).html(this.outputlink(this.page));
  }
}