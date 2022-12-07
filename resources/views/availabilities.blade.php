@php
    /**
    * @var \App\DTO\AvailabilityDataDTO $date
    */
@endphp
        <!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body>
<table class="table table-striped table-bordered">
    <thead class="thead-dark">
    <tr>
        <th scope="col">Date/Nights</th>
        <th scope="col">P</th>
        @foreach(range(1, 21) as $index)
            <th scope="col">{{$index}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($dates as $date)
        @foreach($date->prices as $persons => $prices)
            <tr>
                <th scope="row">{{$date->availability->date->toDateString()}}</th>
                <td>{{$persons}}</td>
                @foreach($prices as $price)
                    <td>{{$price}}</td>
                @endforeach
            </tr>
        @endforeach

    @endforeach
    </tbody>
</table>
{{$dates->links()}}

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>
