{{--
    State + City cascading dropdowns.

    Usage: @include('partials.location-dropdowns', [
        'selectedState' => old('state', $model->state ?? ''),
        'selectedCity' => old('city', $model->city ?? ''),
        'stateField' => 'state',   // optional, default 'state'
        'cityField' => 'city',     // optional, default 'city'
        'required' => true,        // optional
    ])

    Renders two <select> elements. Wrap them in your own grid/flex layout.
--}}
@php
    $stateField = $stateField ?? 'state';
    $cityField = $cityField ?? 'city';
    $selState = $selectedState ?? '';
    $selCity = $selectedCity ?? '';
    $req = !empty($required);
    $states = config('locations.states', []);
    $allCities = config('locations.cities', []);
    $uid = 'lc' . crc32($stateField . $cityField);
@endphp

<select name="{{ $stateField }}" id="s{{ $uid }}" {{ $req ? 'required' : '' }}
    style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
    <option value="">Select State</option>
    @foreach($states as $s)
        <option value="{{ $s }}" {{ $selState === $s ? 'selected' : '' }}>{{ $s }}</option>
    @endforeach
</select>

<select name="{{ $cityField }}" id="c{{ $uid }}" {{ $req ? 'required' : '' }}
    style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
    <option value="">Select City</option>
    @if($selState && isset($allCities[$selState]))
        @foreach($allCities[$selState] as $c)
            <option value="{{ $c }}" {{ $selCity === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    @endif
</select>

<script>
(function(){
    var cm = @json($allCities);
    var ss = document.getElementById('s{{ $uid }}');
    var cs = document.getElementById('c{{ $uid }}');
    var saved = @json($selCity);
    ss.addEventListener('change', function(){
        cs.innerHTML = '<option value="">Select City</option>';
        var list = cm[this.value] || [];
        list.forEach(function(c){
            var o = document.createElement('option');
            o.value = c; o.textContent = c;
            if(c === saved) o.selected = true;
            cs.appendChild(o);
        });
    });
})();
</script>
