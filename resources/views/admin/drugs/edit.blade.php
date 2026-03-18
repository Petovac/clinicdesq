       @extends('admin.layout')

       <style>

       /* Page Typography */

       h2{
       font-size:26px;
       color:#111827;
       margin-bottom:5px;
       }

       h3{
       font-size:20px;
       color:#111827;
       margin-top:25px;
       margin-bottom:10px;
       }

       p{
       font-size:15px;
       line-height:1.6;
       }


       /* Form Layout */

       form{
       background:#ffffff;
       padding:20px;
       border-radius:8px;
       box-shadow:0 1px 3px rgba(0,0,0,0.08);
       margin-top:10px;
       }

       label{
       font-weight:600;
       margin-right:10px;
       font-size:14px;
       color:#374151;
       }

       select,
       input[type="text"],
       input[type="number"]{
       padding:8px 10px;
       border:1px solid #d1d5db;
       border-radius:6px;
       font-size:14px;
       margin-right:8px;
       }

       select:focus,
       input:focus{
       outline:none;
       border-color:#10b981;
       box-shadow:0 0 0 2px rgba(16,185,129,0.15);
       }


       /* Checkbox groups */

       input[type="checkbox"]{
       margin-right:5px;
       }

       label input[type="checkbox"]{
       margin-right:6px;
       }

       div label{
       margin-right:18px;
       font-weight:500;
       }


       /* Buttons */

       button{
       padding:9px 14px;
       background:#10b981;
       color:white;
       border:none;
       border-radius:6px;
       font-weight:600;
       cursor:pointer;
       transition:all .15s ease;
       }

       button:hover{
       background:#059669;
       }


       /* Links */

       a{
       color:#2563eb;
       text-decoration:none;
       font-size:13px;
       }

       a:hover{
       text-decoration:underline;
       }


       /* Table Styling */

       table{
       width:100%;
       background:white;
       border-collapse:collapse;
       margin-top:15px;
       border-radius:8px;
       overflow:hidden;
       box-shadow:0 1px 3px rgba(0,0,0,0.08);
       }

       th{
       text-align:left;
       padding:12px;
       background:#f3f4f6;
       font-size:14px;
       color:#374151;
       }

       td{
       padding:12px;
       border-bottom:1px solid #f1f5f9;
       font-size:14px;
       }

       tr:last-child td{
       border-bottom:none;
       }

       /* Dosage Display */

       .dose-card{
       display:flex;
       align-items:center;
       gap:14px;
       background:#ffffff;
       border:1px solid #e5e7eb;
       border-radius:8px;
       padding:12px 14px;
       margin-bottom:8px;
       box-shadow:0 1px 2px rgba(0,0,0,0.04);
       }

       .dose-species{
       font-weight:600;
       color:#111827;
       min-width:80px;
       }

       .dose-info{
       display:flex;
       align-items:center;
       gap:10px;
       flex-wrap:wrap;
       font-size:14px;
       }

       .dose-value{
       font-weight:500;
       color:#374151;
       }

       .dose-tag{
       font-size:12px;
       padding:4px 8px;
       border-radius:6px;
       font-weight:600;
       }

       .route{
       background:#e0f2fe;
       color:#0369a1;
       }

       .freq{
       background:#ecfdf5;
       color:#047857;
       }

       </style>

       @section('content')

       @php
       $dose = $drug->dosages->first();

       $selectedRoutes = $dose->routes ?? [];
       $selectedFrequencies = $dose->frequencies ?? [];

       /* ensure arrays */
       if(!is_array($selectedRoutes)) {
       $selectedRoutes = [$selectedRoutes];
       }

       if(!is_array($selectedFrequencies)) {
       $selectedFrequencies = [$selectedFrequencies];
       }
       @endphp

       <h2>{{ $drug->name }}</h2>

       <p><strong>Class:</strong> {{ $drug->drug_class }}</p>

       <hr>


       <h3>Dosage</h3>

       @foreach($drug->dosages as $dose)

       @php
       $routes = is_array($dose->routes) ? $dose->routes : (is_string($dose->routes) ? json_decode($dose->routes, true) : []);
       $freq = is_array($dose->frequencies) ? $dose->frequencies : (is_string($dose->frequencies) ? json_decode($dose->frequencies, true) : []);
       @endphp

       <div class="dose-card">

       <div class="dose-species">
       {{ ucfirst($dose->species) }}
       </div>

       <div class="dose-info">

       <span class="dose-value">

       @if($dose->dose_max)
       {{ $dose->dose_min }} – {{ $dose->dose_max }} mg/kg
       @else
       {{ $dose->dose_min }} mg/kg
       @endif

       </span>

       @if(!empty($routes))
       <span class="dose-tag route">
       {{ implode(', ', $routes) }}
       </span>
       @endif

       @if(!empty($freq))
       <span class="dose-tag freq">
       {{ implode(', ', $freq) }}
       </span>
       @endif

       </div>

       </div>

       @endforeach

       <form method="POST" action="/admin/drugs/{{ $drug->id }}/dosage" style="margin-bottom:15px">

       @csrf

       <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">

       <label style="margin:0">Species</label>

       <select name="species"
       style="padding:6px;border:1px solid #ccc;border-radius:4px">

       <option value="dog">Dog</option>
       <option value="cat">Cat</option>
       <option value="rabbit">Rabbit</option>
       <option value="horse">Horse</option>
       <option value="cow">Cow</option>

       </select>

       <input type="number"
       step="0.01"
       name="dose_min"
       placeholder="Dose Min"
       value="{{ $dose->dose_min ?? '' }}"
       style="width:120px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <span>-</span>

       <input type="number"
       step="0.01"
       name="dose_max"
       placeholder="Dose Max"
       value="{{ $dose->dose_max ?? '' }}"
       style="width:120px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <span style="color:#6b7280">mg/kg</span>

       </div>

       <div style="margin-top:6px">

       <label>Route:</label>
       <a href="#" onclick="selectAllRoutes(); return false;">Select All</a> |
       <a href="#" onclick="clearRoutes(); return false;">Clear</a>

       <br>

       <label>
       <input type="checkbox" name="routes[]" value="IV"
       {{ in_array('IV', $selectedRoutes) ? 'checked' : '' }}>
       IV
       </label>

       <label>
       <input type="checkbox" name="routes[]" value="IM"
       {{ in_array('IM', $selectedRoutes) ? 'checked' : '' }}>
       IM
       </label>

       <label>
       <input type="checkbox" name="routes[]" value="SC"
       {{ in_array('SC', $selectedRoutes) ? 'checked' : '' }}>
       SC
       </label>

       <label>
       <input type="checkbox" name="routes[]" value="Oral"
       {{ in_array('Oral', $selectedRoutes) ? 'checked' : '' }}>
       Oral
       </label>

       </div>

       <div style="margin-top:6px">

       <label>Frequency:</label>
       <a href="#" onclick="selectAllFrequency(); return false;">Select All</a> |
       <a href="#" onclick="clearFrequency(); return false;">Clear</a>

       <br>

       <label>
       <input type="checkbox" name="frequencies[]" value="SID"
       {{ in_array('SID', $selectedFrequencies) ? 'checked' : '' }}>
       SID
       </label>

       <label>
       <input type="checkbox" name="frequencies[]" value="BID"
       {{ in_array('BID', $selectedFrequencies) ? 'checked' : '' }}>
       BID
       </label>

       <label>
       <input type="checkbox" name="frequencies[]" value="TID"
       {{ in_array('TID', $selectedFrequencies) ? 'checked' : '' }}>
       TID
       </label>

       <label>
       <input type="checkbox" name="frequencies[]" value="QID"
       {{ in_array('QID', $selectedFrequencies) ? 'checked' : '' }}>
       QID
       </label>

       <label>
       <input type="checkbox" name="frequencies[]" value="Single"
       {{ in_array('Single', $selectedFrequencies) ? 'checked' : '' }}>
       Single Dose
       </label>

       </div>

       <button style="padding:6px 10px;background:#10b981;color:white;border:none;border-radius:4px">
       Save
       </button>

       </form>

       <hr>

       <h3>Products / Formulations</h3>

       <form method="POST" action="/admin/drugs/{{ $drug->id }}/product" style="margin-bottom:15px">

       @csrf

       <select name="form"
              style="padding:6px;border:1px solid #ccc;border-radius:4px">

              <option value="tablet">Tablet</option>
              <option value="capsule">Capsule</option>
              <option value="injection">Injection</option>
              <option value="vial">Vial</option>
              <option value="fluid">Fluid</option>

       </select>

       <input type="text"
              name="brand_name"
              placeholder="Brand (optional)"
              style="width:180px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <input type="number"
              step="0.01"
              name="strength_value"
              placeholder="Strength"
              style="width:120px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <select name="strength_unit"
       style="padding:6px;border:1px solid #ccc;border-radius:4px">

       <option value="mg">mg</option>
       <option value="mg/ml">mg/ml</option>
       <option value="g">g</option>
       <option value="IU">IU</option>

       </select>

       <input type="number"
       step="0.01"
       name="pack_size"
       placeholder="Pack Size"
       style="width:110px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <input type="number"
       step="0.01"
       name="pack_size"
       placeholder="Pack Size"
       style="width:110px;padding:6px;border:1px solid #ccc;border-radius:4px">

       <select name="pack_unit"
              style="padding:6px;border:1px solid #ccc;border-radius:4px">

       <option value="">Unit</option>
       <option value="ml">ml</option>
       <option value="mg">mg</option>
       <option value="g">g</option>
       <option value="Number">Nos.</option>

       </select>

       

       <button style="padding:6px 10px;background:#10b981;color:white;border:none;border-radius:4px">
       Save
       </button>

       </form>

       <table style="width:100%;background:white;border-collapse:collapse">

       <tr style="background:#f9fafb">
       <th style="padding:10px;border-bottom:1px solid #e5e7eb">Form</th>
       <th style="padding:10px;border-bottom:1px solid #e5e7eb">Brand</th>
       <th style="padding:10px;border-bottom:1px solid #e5e7eb">Strength</th>
       <th style="padding:10px;border-bottom:1px solid #e5e7eb">Pack Size (ml/tabs)</th>
       </tr>

       @foreach($drug->brands as $product)

       <tr>

       <td style="padding:10px;border-bottom:1px solid #eee">
       {{ $product->form }}
       </td>

       <td style="padding:10px;border-bottom:1px solid #eee">
       {{ $product->brand_name ?? '-' }}
       </td>

       <td style="padding:10px;border-bottom:1px solid #eee">
       {{ $product->strength_value }} {{ $product->strength_unit }}
       </td>

       <td style="padding:10px;border-bottom:1px solid #eee">

       @if($product->pack_size)
       {{ $product->pack_size }} {{ $product->pack_unit }}
       @else
       -
       @endif

       </td>

       </tr>

       @endforeach

       </table>


       <script>

       function selectAllRoutes(){
       document.querySelectorAll('input[name="routes[]"]').forEach(cb => cb.checked = true);
       }

       function clearRoutes(){
       document.querySelectorAll('input[name="routes[]"]').forEach(cb => cb.checked = false);
       }

       function selectAllFrequency(){
       document.querySelectorAll('input[name="frequencies[]"]').forEach(cb => cb.checked = true);
       }

       function clearFrequency(){
       document.querySelectorAll('input[name="frequencies[]"]').forEach(cb => cb.checked = false);
       }

       </script>
       @endsection