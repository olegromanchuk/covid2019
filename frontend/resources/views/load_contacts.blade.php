@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-10">
                        <form action="/loadcontacts" method="post">
                            @csrf
                            <div class="form-group">
                                Paste contacts here in CSV format: <b>name, phone number, patient name, note<b>. One number per line. Numbers may
                                include spaces, dashes and parentheses.<br>
                                CSV format is a plain text with the values are separated by commas. You can easily export your data from Excel and paste it here.<br>
                                <a href="https://www.youtube.com/watch?v=RW0BTP-XyeY">Video: How to convert Excel file to CSV</a><br>
                                For example:
                                <p>
                                    Joe Dou,2126463444,Francesca Dou,Some note<br>
                                    Alice Ku, 646-122-3456,Ralph Ku,<br>
                                    Fred Ho,(718) 344 5444,Andy Ho,Some interesting information<br>
                                </p>
                                {{--                                <label for="exampleFormControlTextarea1">Paste numbers here. Number,name,campaign</label>--}}
                                <textarea name="numbers" class="form-control" id="exampleFormControlTextarea1"
                                          rows="20" placeholder="Joe Dou,2126463444,Francesca Dou,Some note
Alice Ku, 212-646-3444,,"></textarea>
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
