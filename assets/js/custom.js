jQuery(document).ready(function(){
  jQuery('#empTable').DataTable({
   'pagingType': 'simple_numbers',
    'scrollY': "550px",
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
      'ajax': {
          'url': myajax.ajaxurl + '?action=getpostsfordatatables',
      },
      'columns': [
          { data: 'cert_id' },
          { data: 'cert_serial' },
          { data: 'cert_ek_serial' },
          { data: 'cert_owners' },
          { data: 'cert_status' },
          { data: 'cert_path' },
          { data: 'cert_zip' },
          { data: 'cert_date' },
          { data: 'cert_modified' },
      ]
  });
});
 

