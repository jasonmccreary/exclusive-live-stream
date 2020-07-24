<div class="form-horizontal">
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-3 mb15 @if ( $errors->has('voorletters') ) state-error @endif">
			<label for="voorletters" class="field-label">Voorletters</label>
			{!! Form::text('voorletters', null, ['class' => 'gui-input', 'id' => 'voorletters', 'autocomplete' => 'off']) !!}
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 mb15">
			<label for="tussenvoegsel" class="field-label">Tussenvoegsel</label>
			{!! Form::text('tussenvoegsel', null, ['class' => 'gui-input', 'id' => 'tussenvoegsel', 'autocomplete' => 'off']) !!}
		</div>
		<div class="col-xs-12 col-md-3 mb15 @if ( $errors->has('achternaam') ) state-error @endif">
			<label for="achternaam" class="field-label">Achternaam</label>
			{!! Form::text('achternaam', null, ['class' => 'gui-input', 'id' => 'achternaam', 'autocomplete' => 'off']) !!}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 col-sm-3 mb15 @if ( $errors->has('postcode') ) state-error @endif">
			<label for="postcode" class="field-label">Postcode</label>
			{!! Form::text('postcode', null, ['class' => 'gui-input', 'id' => 'postcode', 'autocomplete' => 'off']) !!}
		</div>
		<div class="col-xs-6 col-sm-3 mb15 @if ( $errors->has('huisnummer') ) state-error @endif">
			<label for="huisnummer" class="field-label">Huisnummer</label>
			{!! Form::text('huisnummer', null, ['class' => 'gui-input', 'id' => 'huisnummer', 'autocomplete' => 'off']) !!}
		</div>
		<div class="col-xs-6 col-md-3 col-lg-4 col-xl-3 mb15">
			<label for="toevoeging" class="field-label">Huisnummer toevoeging</label>
			{!! Form::text('toevoeging', null, ['class' => 'gui-input', 'id' => 'toevoeging', 'autocomplete' => 'off']) !!}
		</div>
	</div>

	@include('partials.address-failed', ['class' => 'addressFail'])

	@if(auth()->user()->establishment->canGetMeterReadings())
		<div class="row">
			<div class="col-xs-12 mb15">
				<label for="meterstanden_uitlezen_uit" class="field-label">Mogen wij de meterstanden uitlezen?</label>
				<div class="col-xs-12 col-md-3 pln">
					<div class="checkbox-custom checkbox-primary mb5">
						{!! Form::checkbox('meterstanden_uitlezen_uit', 1, (isset($client) and isset($client->energy_data) and is_null($client->energy_data->meterstanden_uitlezen_uit)) ? true : false, ['id' => 'meterstanden_uitlezen_uit']) !!}
						<label for="meterstanden_uitlezen_uit">Ja</label>
					</div>
				</div>
			</div>
		</div>
	@endif

	@role('admin')
	<div class="row">
		<div class="section-divider mb25">
			<span>Admin only</span>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6 col-md-3 mb15">
			<label for="eancode_elektra" class="field-label">Eancode elektra</label>
			{!! Form::text('eancode_elektra', (isset($client) and isset($client->energy_data)) ? $client->energy_data->eancode_elektra : null , ['class' => 'gui-input', 'autocomplete' => 'off', 'id' => 'eancode_elektra']) !!}
		</div>

		<div class="col-xs-6 col-md-3 mb15">
			<label for="eancode_gas" class="field-label">Eancode gas</label>
			{!! Form::text('eancode_gas', (isset($client) and isset($client->energy_data)) ? $client->energy_data->eancode_gas : null , ['class' => 'gui-input', 'autocomplete' => 'off', 'id' => 'eancode_gas']) !!}
		</div>
	</div>
	@endrole
</div>

<div id="clientInZorgcentrum" class="mfp-hide popup-basic popup-basic admin-form mfp-with-anim">
	<div class="panel">
		<div class="panel-heading">
			<span class="panel-title">Let op</span>
		</div>

		<div class="panel-body">
			<p>Het lijkt er op dat deze cli&euml;nt in een zorginstelling zit. <br/>Klopt dat?</p>
		</div>

		<div class="panel-footer text-right">
			<a class="btn btn-success inZorginstelling">Ja</a>
			<a class="btn btn-danger closePopup">Nee</a>
		</div><!-- /.panel-footer -->

	</div>
</div>

@push('scripts')
	<script type="text/javascript">
		$(document).on('keyup', '.formatPercentage', function () {
			this.value = this.value.replace(/,/g, '.');
		});

		// ...
	</script>
@endpush
