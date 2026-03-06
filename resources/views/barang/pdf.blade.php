<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 0; 
        }

        body {
            font-family: Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
        }

        .kertas-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            
            padding-top: 1mm; 
            
            padding-left: 0; 
        }

        table {
            border-collapse: separate;
            
            border-spacing: 3mm 2mm; 
            
            margin: 0; 
            table-layout: fixed;
        }

        td {
            
            width: 37.9mm; 
            height: 18mm;
            
            text-align: center;
            vertical-align: middle;
            padding: 0;
            overflow: hidden;
            
            border: 1px solid #ddd; 
            box-sizing: border-box;
        }

        .nama { font-weight: bold; font-size: 6px; margin-bottom: 2px; display: block; white-space: nowrap; overflow: hidden; }
        .harga { font-size: 9px; font-weight: bold; margin-bottom: 2px; }
        .id { font-size: 4px; color: #555; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    {{-- Kita potong dulu datanya per 40 item biar halamannya nggak tumpah (karena pakai absolute position) --}}
    @foreach(array_chunk($dataCetak, 40) as $page)

        <div class="kertas-wrapper">
            <table>
                {{-- Nah, di dalam sini murni pakai logika array_chunk per 5 baris persis kayak Kode Keduamu! --}}
                @foreach(array_chunk($page, 5) as $baris)
                    <tr>
                        @foreach($baris as $item)
                            @if($item != null)
                                <td>
                                    <div class="tag-box">
                                        <div class="nama">
                                            {{ \Illuminate\Support\Str::limit($item->nama, 20) }}
                                        </div>
                                        <div class="harga">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </div>
                                        <div class="id">
                                            {{ $item->id_barang }}
                                        </div>
                                    </div>
                                </td>
                            @else
                                <td style="border: none;"></td>
                            @endif
                        @endforeach
                        
                        {{-- Logika penambal kolom kosong dari kodemu yang kedua --}}
                        @for($i = count($baris); $i < 5; $i++)
                            <td style="border: none;"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>

</html>