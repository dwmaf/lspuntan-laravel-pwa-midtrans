<x-admin-layout>
    <table>
            <thead>
                    <tr>
                        <th >No</th>
                        <th >Nama Asesi</th>
                        <th >Status</th>
                        <th >Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asesis as $asesi)
                    <tr>
                        <td >{{ $loop->iteration}}</td>
                        <td >{{ $asesi->id }}</td>
                        <td >{{ $asesi->status }}</td>
                        <td >
                            <a href="/rincian_data_asesi/{{ $asesi->id }}">Lihat data</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    
</x-admin-layout>