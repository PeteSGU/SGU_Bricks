jQuery(document).ready(function($) {
    
    var totalDeletedOverall = 0;

    /**
     * Recursive function to process deletion in chunks
     */
    function processDeletionBatch() {
        var $log = $('#results-area');
        
        $.post(atqat_ajax.ajax_url, {
            action: 'som_delete',
            security: atqat_ajax.nonce
        }, function(res) {
            if (res.success) {
                totalDeletedOverall += res.data.deleted;
                
                if (res.data.done || res.data.remaining <= 0) {
                    $log.append('<p style="color: green; font-weight: bold;">[SUCCESS] Cleanup complete. Total deleted: ' + totalDeletedOverall + '</p>');
                    $('#run-delete').prop('disabled', false).text('Execute Batched Deletion');
                } else {
                    $log.append('<p>Progress: Deleted ' + totalDeletedOverall + '... (' + res.data.remaining + ' remaining)</p>');
                    // Auto-scroll the log
                    $log.scrollTop($log[0].scrollHeight);
                    // Start next batch
                    processDeletionBatch();
                }
            } else {
                $log.append('<p style="color:red;">Error: ' + res.data + '</p>');
                $('#run-delete').prop('disabled', false).text('Execute Batched Deletion');
            }
        }).fail(function() {
            $log.append('<p style="color:orange;">Server timeout/hiccup. Retrying next batch in 5 seconds...</p>');
            setTimeout(processDeletionBatch, 5000);
        });
    }

    // Handle Preview Button
    $('#run-query').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $log = $('#results-area');
        $btn.prop('disabled', true).text('Working...');
        $log.html('<p>Generating preview list...</p>');

        $.post(atqat_ajax.ajax_url, {
            action: 'som_preview',
            security: atqat_ajax.nonce
        }, function(res) {
            $log.html(res.data);
            $btn.prop('disabled', false).text('Preview Faculty to Keep');
        });
    });

    // Handle Delete Button
    $('#run-delete').on('click', function(e) {
        e.preventDefault();
        if (!confirm("Start batch deletion? This will process 50 records at a time to avoid server errors.")) return;
        
        $(this).prop('disabled', true).text('Processing Batches...');
        $('#results-area').html('<p>Initializing batch deletion...</p>');
        totalDeletedOverall = 0;
        processDeletionBatch();
    });

});