var oTable;
var timeoutObj;
var mainSelectElement = '#mainSelect';

$(function() {
  $('#myTab a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });  
 $('#myTab a:first').tab('show');
 oTable = $('#listTable').dataTable( {
    "sDom": "<'row'<'span12'f>r><'row'<'span12 adActions'>>t<'row'<'span6'li><'span6'p>>",
    "sPaginationType": "bootstrap",
    'aaSorting':[[0, 'asc']],
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page",
      "sEmptyTable": "No transactions",
    },
    "iDisplayLength":10,
    "aoColumns": [
                {
                  "sTitle": "Bank Account",
                  "mData": "BankAccount.name",
                  "sWidth": "200px",
                  "mRender": function( data, type, full ) {
                    var theName = jQuery('<div />').text(data).html();
                    return theName;
                  },
                },
                {
                  "sTitle": "Date",
                  "mData": "Transaction.date",
                  "sWidth": "100px",
                  "mRender": function( data, type, full ) {
                    var theName = jQuery('<div />').text(data).html();
                    return theName;
                  },
                },
                {
                  "sTitle": "Label",
                  "mData": "Transaction.label",
                  "sWidth": "300px",
                  "mRender": function( data, type, full ) {
                    var theName = jQuery('<div />').text(data).html();
                    return theName;
                  },
                },
                {
                  "sTitle": "Increase",
                  "mData": "Transaction.amount",
                  "mRender": function( data, type, full ) {
                    if (full.Transaction.transaction_type_id == 2)
                    {
                      return data;
                    }
                    return '';
                  },
                  "sType": "formatted-num"
                },
                {
                  "sTitle": "Decrease",
                  "mData": "Transaction.amount",
                  "mRender": function( data, type, full ) {
                    if (full.Transaction.transaction_type_id == 1)
                    {
                      return data;
                    }
                    return '';
                  },
                  "sType": "formatted-num"
                },
                {
                  "sTitle": "Balance",
                  "mData": "Transaction.bank_account_after",
                  "mRender": function( data, type, full ) {
                    return data;
                    // TODO: implement formatNumber return formatNumber(data);
                  },
                  "sType": "formatted-num"
                }
              ]
      ,
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
          // init search box
          //initDatatableSearchBox($('#' + $(this).attr('id') + '_wrapper'));
        }
  } );  
  
  getData();
  
});

/**
 * Populate the datatable with data
 * 
 */

function getData() {
  data = {
      consistent_read: "false"
  };
  theUrl = '/transactions/get_list';
  oTable.fnClearTable();
  $(".dataTables_empty").html('<img width="16" height="16" src="http://d3er7671q71co0.cloudfront.net/img/spinner.gif" /> <b>Loading data...</b>');
  
  var jqxhr = $.ajax({
      url: theUrl,
      type: "POST",
      data: JSON.stringify(data),
      cache: false,
      dataType: 'json',
      contentType: "application/json; charset=utf-8"
    })
    .done(function(data, textStatus, jqXHR) {
      if(!data.transactions){
        alert("Error getting data. Please re-load the page and try again. Code: " + code + " ResponseTxt: " + jqXHR.responseText + ".Data: \n" + data);
      }
      if(data.transactions.length == 0) {
        oTable.fnClearTable();
      }
      
      oTable.fnAddData(data.transactions);
    })
      .fail(function(jqXHR, textStatus, errorThrown) {
        alert("Error getting data. Msg: " + textStatus + " ResponseTxt: " + jqXHR.responseText);
      })
      .always(function() { 
        
      });       
}
