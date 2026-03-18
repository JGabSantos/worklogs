<h1>Editar registo</h1>

@if ($errors->has('error'))
    <div style="color: red;">
        {{ $errors->first('error') }}
    </div>
@endif

<form method="POST" action="{{ route('time-entries.update', $timeEntry->id) }}">
    @csrf
    @method('PUT')

    <div>
        <label>Data</label>
        <input type="date" name="date" value="{{ old('date', $timeEntry->date->format('Y-m-d')) }}">
    </div>

    <div>
        <label>Hora início</label>
        <input type="time" name="start_time" step="1" value="{{ old('start_time', $timeEntry->start_time) }}">
    </div>

    <div>
        <label>Hora fim</label>
        <input type="time" name="end_time" step="1" value="{{ old('end_time', $timeEntry->end_time) }}">
    </div>

    <div>
        <label>Local</label>
        <input type="text" name="location" value="{{ old('location', $timeEntry->location) }}">
    </div>

    <div>
        <label>Tipo de atividade</label>
        <select name="activity_type_id">
            @foreach ($activityTypes as $activityType)
                <option value="{{ $activityType->id }}" @selected(old('activity_type_id', $timeEntry->activity_type_id) == $activityType->id)>
                    {{ $activityType->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Cliente</label>
        <select name="client_id">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $timeEntry->client_id) == $client->id)>
                    {{ $client->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Descrição</label>
        <textarea name="description">{{ old('description', $timeEntry->description) }}</textarea>
    </div>

    <div>
        <label>Estado</label>
        <select name="status">
            <option value="draft" @selected(old('status', $timeEntry->status) === 'draft')>Rascunho</option>
            <option value="active" @selected(old('status', $timeEntry->status) === 'active')>Ativo</option>
        </select>
    </div>

    <button type="submit">Atualizar</button>
</form>
