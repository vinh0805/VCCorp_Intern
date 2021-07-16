<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 3 | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Global stylesheets -->
{{--    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"--}}
{{--          type="text/css">--}}
    <link href="{{asset('assets/css/icons/icomoon/styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/core.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <link href="{{asset('css/stylesheet.css')}}" rel="stylesheet">

    <!-- Core JS files -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/pace.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/blockui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/validation/validate.min.js')}}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/styling/uniform.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('assets/js/core/app.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/form_inputs.js')}}"></script>
    <!-- /theme JS files -->

</head>
<body>
@yield('content')

<script type="text/javascript">
    $().ready(function () {
        let sign_up_form = $('.validate-form-2');
        sign_up_form.validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                    regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
                },
                email: {
                    required: true,
                    maxlength: 50,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            }
        });


        sign_up_form.submit( function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            let form = $(this);
            let url = form.attr('action');

            // check if the input is valid using a 'valid' property
            if (form.valid()) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    contentType: false,       // The content type used when sending data to the server.
                    cache: false,             // To unable request pages to be cached
                    processData: false,        // To send DOMDocument or non processed data file it is set to false
                    success: function (data) {
                        if (data.message) {
                            alert(data.message); // show response from the php script.
                        }
                        if (data.success) {
                            console.log(data.url);
                            window.location.replace(data.url);
                        }
                    }
                });
            }

        })

        let sign_in_form = $('.validate-form-sign-in');
        sign_in_form.validate({
            ignore: [],
            rules: {
                email: {
                    required: true,
                    maxlength: 50,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                }
            }
        });


        sign_in_form.submit( function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            let form = $(this);
            let url = form.attr('action');

            // check if the input is valid using a 'valid' property
            if (form.valid()) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    contentType: false,       // The content type used when sending data to the server.
                    cache: false,             // To unable request pages to be cached
                    processData: false,        // To send DOMDocument or non processed data file it is set to false
                    success: function (data) {
                        if (data.message) {
                            alert(data.message); // show response from the php script.
                        }
                        if (data.success) {
                            console.log(data.url);
                            window.location.replace(data.url);
                        }
                    }
                });
            }

        })

    })

    jQuery.extend(jQuery.validator.messages, {
        required: "Bạn cần phải nhập trường này!",
        email: "Hãy nhập đúng định dạng email!",
        maxlength: jQuery.validator.format("Tối đa {0} ký tự."),
        minlength: jQuery.validator.format("Ít nhất {0} ký tự."),
        equalTo: "Mật khẩu không trùng khớp!"
    });

    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            return this.optional(element) || regexp.test(value);
        },
        "Sai định dạng dữ liệu!"
    );








</script>
</body>
</html>
