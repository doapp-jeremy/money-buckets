var oTable;
var timeoutObj;
var mainSelectElement = '#mainSelect';
$(function() {  
 oTable = $('#bankAccountsTable').dataTable( {
    "sDom": "<'row'<'span12'f>r><'row'<'span12 adActions'>>t<'row'<'span6'li><'span6'p>>",
    "sPaginationType": "bootstrap",
    'aaSorting':[[0, 'asc']],
    "oLanguage": {
      "sLengthMenu": "_MENU_ records per page",
      "sEmptyTable": "No bank accounts",
    },
    "iDisplayLength":10,
    "aoColumns": [
                {
                  "sTitle": "Name",
                  "mData": "BankAccount.name",
                  "sWidth": "200px",
                  "mRender": function( data, type, full ) {
                    var theName = jQuery('<div />').text(data).html();
                    return theName;
                  },
                },
                {
                  "sTitle": "Current Balance",
                  "mData": "BankAccount.current_balance",
                  "mRender": function( data, type, full ) {
                    return data;
                    // TODO: implement formatNumber return formatNumber(data);
                  },
                  "sType": "formatted-num"
                },
                {
                  "sTitle": "Unallocated Balance",
                  "mData": "BankAccount.unallocated_balance",
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
  
  loadBankAccounts();
  
});

/**
 * Populate the datatable with data
 * 
 */

function loadBankAccounts() {
  data = {
      consistent_read: "false"
  };
  theUrl = '/bank_accounts/get_list';
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
      if(!data.bank_accounts){
        alert("Error getting data. Please re-load the page and try again. Code: " + code + " ResponseTxt: " + jqXHR.responseText + ".Data: \n" + data);
      }
      if(data.bank_accounts.length == 0) {
        oTable.fnClearTable();
      }
      
      oTable.fnAddData(data.bank_accounts);
    })
      .fail(function(jqXHR, textStatus, errorThrown) {
        alert("Error getting data. Msg: " + textStatus + " ResponseTxt: " + jqXHR.responseText);
      })
      .always(function() { 
        
      });       
}
