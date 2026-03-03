<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Tag Harga Barang</title>
    <style>
        @page { 
            size: 210mm 165mm; 
            margin: 0; 
        }
        
        body { 
            font-family: 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 0; 
            color: #000; 
        }
        
        .kertas-wrapper {
            padding-left: 8.5mm;  
            padding-top: 3.5mm; 
        }

        table { 
            border-collapse: separate; 
            border-spacing: 3mm 3mm; 
            table-layout: fixed; 
        }
        
        td { 
            width: 37mm; 
            height: 18mm; 
            
            text-align: center; 
            vertical-align: middle; 
            padding: 0; 
            overflow: hidden;
        }

        td.stiker-isi {
            border: none;
        }

        td.stiker-kosong {
            border: none; 
        }
        
        .nama { font-weight: bold; font-size: 8px; margin-bottom: 2px; display: block; white-space: nowrap; overflow: hidden; }
        .harga { font-size: 11px; font-weight: bold; margin-bottom: 2px; }
        .id { font-size: 6px; color: #555; }
    </style>
</head>
<body>
    <div class="kertas-wrapper">
        <table>
            @foreach(array_chunk($dataCetak, 5) as $baris)
            <tr>
                @foreach($baris as $item)
                    @if($item != null)
                        <td class="stiker-isi">
                            <span class="nama">{{ \Illuminate\Support\Str::limit($item->nama, 20) }}</span>
                            <div class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                            <div class="id">{{ $item->id_barang }}</div>
                        </td>
                    @else
                        <td class="stiker-kosong"></td>
                    @endif
                @endforeach
                
                @for($i = count($baris); $i < 5; $i++)
                    <td class="stiker-kosong"></td>
                @endfor
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>