<h1>Os meus registos</h1>

@if (session('success'))
    <div>{{ session('success') }}</div>
@endif

<a href="{{ route('time-entries.create') }}">Novo registo</a>

@if ($timeEntries->isEmpty())
    <p>Sem registos.</p>
@else
    <form method="GET" action="{{ route('time-entries.index') }}">
        <div>
            <label>Data</label>
            <input type="date" name="date" value="{{ request('date') }}">
        </div>

        <div>
            <label>Estado</label>
            <select name="status">
                <option value="">Todos</option>
                <option value="draft" @selected(request('status') === 'draft')>Rascunho</option>
                <option value="active" @selected(request('status') === 'active')>Ativo</option>
            </select>
        </div>

        <div>
            <label>Cliente</label>
            <select name="client_id">
                <option value="">Todos</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected((string) request('client_id') === (string) $client->id)>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Tipo de atividade</label>
            <select name="activity_type_id">
                <option value="">Todos</option>
                @foreach ($activityTypes as $activityType)
                    <option value="{{ $activityType->id }}" @selected((string) request('activity_type_id') === (string) $activityType->id)>
                        {{ $activityType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit">Filtrar</button>
    </form>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Data</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Duração</th>
                <th>Local</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeEntries as $entry)
                <tr>
                    <td>{{ $entry->date->format('Y-m-d') }}</td>
                    <td>{{ $entry->start_time }}</td>
                    <td>{{ $entry->end_time }}</td>
                    <td>{{ $entry->duration_minutes }} min</td>
                    <td>{{ $entry->location }}</td>
                    <td>{{ $entry->activityType->name }}</td>
                    <td>{{ $entry->client->name }}</td>
                    <td>{{ $entry->status }}</td>
                    <td>
                        <a href="{{ route('time-entries.show', $entry->id) }}">Ver</a>

                        <br><br>

                        <a href="{{ route('time-entries.edit', $entry->id) }}">Editar</a>

                        <br><br>

                        <form method="POST" action="{{ route('time-entries.destroy', $entry->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Apagar este registo?')">
                                Apagar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $timeEntries->links() }}
@endif
