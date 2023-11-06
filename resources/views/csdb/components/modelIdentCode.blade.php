@props(['checked'])
<h5>Model Ident Code</h5>
<div class="form-check">
    <input class="form-check-input" type="radio" name="modelIdentCode" id="modelIdentCode_N219" value="N219" {{ strtoupper($checked) == 'N219' ? 'checked' : '' }}>
    <label class="form-check-label" for="modelIdentCode_N219">
      N219
    </label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="modelIdentCode" id="modelIdentCode_MALE" value="MALE" {{ strtoupper($checked) == 'MALE' ? 'checked' : '' }}>
    <label class="form-check-label" for="modelIdentCode_MALE">
      MALE
    </label>
</div>