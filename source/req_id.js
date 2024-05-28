// When the modal is about to be shown
$('#deleteModal').on('show.bs.modal', function (event) {
    // Get the button that triggered the modal
    var button = $(event.relatedTarget);
    // Extract the request id from the data-* attribute
    var requestId = button.data('request-id');
    // Update the modal's form
    var modal = $(this);
    modal.find('#deleteRequestId').val(requestId);
});