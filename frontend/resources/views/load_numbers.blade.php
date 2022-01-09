@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-10">
                        <form action="/load/1" method="post">
                            @csrf
                            <div class="form-group">
                                Paste numbers here in CSV format: <b>number,name,campaign<b>. One number per line. Numbers may
                                include spaces, dashes and parentheses.<br>
                                CSV format is a plain text with the values are separated by commas. You can easily export your data from Excel and paste it here.<br>
                                <a href="https://www.youtube.com/watch?v=RW0BTP-XyeY">Video: How to convert Excel file to CSV</a><br>
                                For example:
                                <p>
                                    2126463444,Joe Dou,1<br>
                                    212-646-3444,Alice Ku,1<br>
                                    (212) 646 3444,Fred Ho,1
                                </p>
                                {{--                                <label for="exampleFormControlTextarea1">Paste numbers here. Number,name,campaign</label>--}}
                                <textarea name="numbers" class="form-control" id="exampleFormControlTextarea1"
                                          rows="20" placeholder="2126463444,Joe Dou,1
212-646-3444,Alice Ku,1"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
