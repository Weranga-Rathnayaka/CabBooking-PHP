 <!-- Header-->
 <!-- <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder mx-5">About Us</h1>
        </div>
    </div>
</header> -->
<!-- <section class="py-5">
    <div class="container">
        <div class="card rounded-0 card-outline card-purple shadow px-4 px-lg-5 mt-5">
            <div class="row">
            <div class="card-body">
                <h4>Contact Form</h4>
            </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).scroll(function() { 
        $('#topNavBar').removeClass('bg-purple navbar-light navbar-dark bg-gradient-purple text-light')
        if($(window).scrollTop() === 0) {
           $('#topNavBar').addClass('navbar-dark bg-purple text-light')
        }else{
           $('#topNavBar').addClass('navbar-dark bg-gradient-purple ')
        }
    });
    $(function(){
        $(document).trigger('scroll')
    })
</script> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Chatbot Integration</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <!-- Header (Optional: Uncomment if needed) -->
    <!-- <header class="bg-dark py-5" id="main-header">
        <div class="container h-100 d-flex align-items-end justify-content-center w-100">
            <div class="text-center text-white w-100">
                <h1 class="display-4 fw-bolder mx-5">About Us</h1>
            </div>
        </div>
    </header> -->

    <!-- Contact Form Section -->
    <section class="py-5">
        <div class="container">
            <div class="">
                <div class="row">
                    <div class="card-body">
                        <h4></h4>
                        <!-- Chatbot UI -->
                        <div class="wrapper">
                            <div class="title">Chat with Us</div>
                            <div class="form">
                                <div class="bot-inbox inbox">
                                    <div class="icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="msg-header">
                                        <p>Hello there, how can I help you?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="typing-field">
                                <div class="input-data">
                                    <input id="data" type="text" placeholder="Type something here.." required>
                                    <button id="send-btn">Send</button>
                                </div>
                            </div>
                        </div>
                        <!-- End of Chatbot UI -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navbar Scroll Script -->
    <script>
        $(document).scroll(function() { 
            $('#topNavBar').removeClass('bg-purple navbar-light navbar-dark bg-gradient-purple text-light')
            if($(window).scrollTop() === 0) {
               $('#topNavBar').addClass('navbar-dark bg-purple text-light')
            } else {
               $('#topNavBar').addClass('navbar-dark bg-gradient-purple ')
            }
        });
        $(function(){
            $(document).trigger('scroll')
        })
    </script>

    <!-- Chatbot AJAX Script -->
    <script>
        $(document).ready(function(){
            $("#send-btn").on("click", function(){
                $value = $("#data").val();
                $msg = '<div class="user-inbox inbox"><div class="msg-header"><p>'+ $value +'</p></div></div>';
                $(".form").append($msg);
                $("#data").val('');
                
                // Start AJAX code
                $.ajax({
                    url: 'message.php',
                    type: 'POST',
                    data: 'text='+$value,
                    success: function(result){
                        $replay = '<div class="bot-inbox inbox"><div class="icon"><i class="fas fa-user"></i></div><div class="msg-header"><p>'+ result +'</p></div></div>';
                        $(".form").append($replay);
                        // When chat goes down, the scroll bar automatically moves to the bottom
                        $(".form").scrollTop($(".form")[0].scrollHeight);
                    }
                });
            });
        });
    </script>
</body>
</html>
