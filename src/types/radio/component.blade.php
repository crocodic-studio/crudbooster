@extends("types::layout")
@section("content")
        <?php /** @var \crocodicstudio\crudbooster\types\radio\RadioModel $column */ ?>
        @foreach($column->getOptions() as $key=>$value)
            <div class="{{ $column->getDisabled()?"disabled":"" }}">
                <label class='radio-inline'>
                    <input type="radio"
                            {{ $column->getDisabled()?"disabled":"" }}
                            {{ $column->getValue() == $key?"checked":"" }}
                           name="{{ $column->getName() }}"
                           value="{{ $key }}"> {{ $value }}
                </label>
            </div>
        @endforeach
@endsection