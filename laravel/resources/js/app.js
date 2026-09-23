import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import jQuery from 'jquery';
import DataTable from 'datatables.net-bs5/js/dataTables.bootstrap5';

DataTable.use(jQuery);
window.$ = jQuery;
window.DataTable = DataTable;
