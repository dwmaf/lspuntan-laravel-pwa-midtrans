Agar data dari eloquent bisa diterima oleh alpine, perlu dilakukan hal berikut
JSON.parse('{{ json_encode($skemas) }}'),