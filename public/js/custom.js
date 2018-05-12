$(document).ready(function () {
    // Activate tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // Select/Deselect checkboxes
    var checkbox = $('table tbody input[type="checkbox"]');
    $("#selectAll").click(function () {
        if (this.checked) {
            checkbox.each(function () {
                this.checked = true;
            });
        } else {
            checkbox.each(function () {
                this.checked = false;
            });
        }
    });
    checkbox.click(function () {
        if (!this.checked) {
            $("#selectAll").prop("checked", false);
        }
    });

    //show/hide customers select element
    $('#customers-checkbox').click(function () {
        var customersBlock = $('#customers-form-group');
        customersBlock.toggle();
    });

    //Delete user
    $('.delete').on('click', function () {
        var userId = $(this).data('user-id');

        $('#delete-user-form').attr('action', '/users/' + userId + '/delete');
    });

    //modal with information for update
    //jQuery function for loading data
    $("#editUserModal").on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);

        //bootstrap way of retrieving data-* attributes
        var userId = button.data('user-id');

        $.ajax({
            url: location.origin + '/users/' + userId + '/edit',
            method: "POST",
            data: {userId: userId},
            dataType: "json",
            success: function (data) {
                $('#update-user-id').val(data.user.ID);
                $('#update-username').val(data.user.USER_NAME);
                $('#update-name').val(data.user.USER_REALNAME);
                $('#update-email').val(data.user.USER_EMAIL);
                $('#update-phone').val(data.user.USER_PHONE);

                //update admin checkbox
                var updateIsAdmin = $('#update-is-admin');
                if (data.user.IS_ADMIN === "1") {
                    updateIsAdmin.attr('checked'); // Checks it
                    updateIsAdmin.prop('checked', true);
                } else {
                    updateIsAdmin.removeAttr('checked');
                }

                //update additional information checkbox
                var updateIsDopInfo = $('#update-is-dop-info');
                if (data.user.IS_DOPINFO === "1") {
                    updateIsDopInfo.attr('checked'); // Checks it
                    updateIsDopInfo.prop('checked', true);
                } else {
                    updateIsDopInfo.removeAttr('checked');
                }

                //update customer checkbox and select element with customers
                var updateCustomerCheckbox = $('#update-customer-checkbox');
                var updateSelectCustomers = $('#update-customers-form-group');

                if (data.user.IS_CUST === "1") {
                    updateCustomerCheckbox.attr('checked'); // Checks it
                    updateCustomerCheckbox.prop('checked', true);

                    $('#update-customers-select option[value=' + data.user.ID_CUSTOMER + ']').attr('selected','selected');
                    updateSelectCustomers.show();

                } else {
                    updateCustomerCheckbox.removeAttr('checked');
                    updateSelectCustomers.hide();
                }

                //update form action
                $('#update-user-form').attr('action', '/users/' + userId + '/update');

            }
        });
    });

    //show/hide select element with customers on update modal
    $('#update-customer-checkbox').click(function () {
        var customersBlock = $('#update-customers-form-group');
        customersBlock.toggle();
    });
});