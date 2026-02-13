
$.post('handleform.php', { action: 'fetchLogs' }, function (logs) {

    let html = '';

    logs.forEach(log => {
        let actionClass = 'text-secondary';

        if (log.action === 'create') actionClass = 'text-success';
        if (log.action === 'update') actionClass = 'text-warning';
        if (log.action === 'delete') actionClass = 'text-danger';

        html += `
            <div class="col-xl-6 p-3 col-md-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="${actionClass} text-uppercase font-weight-bold">
                                ${log.action}
                            </span>
                            <small class="text-muted">${log.created_at}</small>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Actor:</small>
                            <span>${log.actor}</span>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Updated Columns:</small>
                            <span>${log.columns_updated ?? '-'}</span>
                        </div>

                        <button class="btn btn-sm btn-outline-info view-log"
                            data-id="${log.id}">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    $('#logsContainer').html(html);

}, 'json');
$(document).on('click', '.view-log', function () {

    let id = $(this).attr('data-id');
    console.log("Clicked ID:", id);

    if (!id) {
        alert("ID is missing!");
        return;
    }

    $.post('handleform.php', {
        action: 'fetchSingleLog',
        log_id: id
    }, function (log) {

        console.log("Single log response:", log);

        if (!log) {
            alert("No log returned!");
            return;
        }

        $('#logBefore').text(log.values_before);
        $('#logAfter').text(log.values_after);

        $('#logModal').modal('show');

    }, 'json');
});
