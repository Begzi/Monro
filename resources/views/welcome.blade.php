<!DOCTYPE html>
<html lang="en">

    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Site Metas -->
    <title>CINEMA - test site for you</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">

    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700" rel="stylesheet"> 
    <link rel="stylesheet" href="{{ asset('assets/front/css/front.css') }}">


</head>
<body>

    <div id="wrapper">
        <header class="market-header header">
            <div class="container-fluid">
                <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
                    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <a class="navbar-brand" href="#"><img src="/assets/front/images/version/market-logo.png" alt=""></a>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <h3>Добро пожаловать на сайт. Внизу есть поле ввода текста. Введите ссылку на PDF-файл.</h3>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div><!-- end container-fluid -->
        </header><!-- end market-header -->


    <hr class="invis">


        <section class="section">
            <div class="container">
                <div class="row">


                    <div class="row">
                        <div class="container">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{route('parse')}}" method="post">
                                @csrf

                                <div class="input-group mb-3">
                                    <input type="text" name="URL" class="form-control" placeholder="URL pdf" value="{{ old('URL') }}">
                                </div>

                                <div class="row">
                                    <!-- /.col -->
                                    <div class="col-4 offset-8">
                                        <button type="submit" class="btn btn-primary">Отправить</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>
                            

                        </div>
                    </div>
                    <br>

                    <hr class="invis">

                    @if (isset($main_array))
                        <div class="ml-4">
                            <h1>Основная информация</h1>
                            @foreach ($main_array as $main)
                                <div> {{ $main }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if (isset($experience_array))
                        <div class="ml-4">
                        <h1>Опыт работы</h1>
                            @foreach ($experience_array as $experience)
                                <div> {{ $experience }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if (isset($skills_array))
                        <div class="ml-4">
                        <h1>Навыки</h1>
                            @foreach ($skills_array as $skill)
                                <div> {{ $skill }}</div>
                            @endforeach
                        </div>
                    @endif



                </div><!-- end row -->
            </div><!-- end container -->
        </section>


        <div class="dmtop">Scroll to Top</div>
        
    </div><!-- end wrapper -->
<script src="{{ asset('assets/front/js/front.js') }}"></script>

</body>
</html>