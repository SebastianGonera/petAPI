<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>pet API</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
<h1>Wyszukaj zwierzę po ID</h1>
<form id="petForm">
    <label for="petId">ID Zwierzęcia:</label>
    <input type="text" id="petId" name="petId" required>
    <button type="submit">Szukaj</button>
</form>

<div id="petData"></div>

<h1>Wyszukaj zwierzę po statusie</h1>
<form id="statusForm">
    <label>
        <input type="checkbox" name="status" value="available"> Available
    </label><br>
    <label>
        <input type="checkbox" name="status" value="pending"> Pending
    </label><br>
    <label>
        <input type="checkbox" name="status" value="sold"> Sold
    </label><br>

    <button type="submit">Zatwierdź</button>
</form>

<div id="selectedStatuses">
</div>


<h1>Usuń zwierzę</h1>
<form id="deleteForm">
    <label for="delId">ID Zwierzęcia:</label>
    <input type="text" id="delId" name="delId" required>
    <button type="submit">Usuń</button>
</form>

<div id="delSt"></div>

<h1>Dodaj zdjęcie</h1>
<form id="uploadForm" enctype="multipart/form-data">
    <label for="petId1">ID zwierzęcia:</label>
    <input type="text" id="petId1" name="petId1" required><br><br>

    <label for="image">Wybierz zdjęcie:</label>
    <input type="file" id="image" name="image" accept="image/*" required><br><br>

    <button type="submit">Wyślij zdjęcie</button>
</form>

<div id="response"></div>

<h1>Aktualizuj zwierzę w sklepie</h1>
<form id="updateForm">
    <label for="petId2">ID zwierzęcia:</label>
    <input type="text" id="petId2" name="petId2" required><br><br>
    <label for="name">Nazwa:</label>
    <input type="text" id="name" name="name" value="ChangedName" required><br><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="pending" selected>Oczekujący</option>
        <option value="available">Dostępny</option>
        <option value="sold">Sprzedany</option>
    </select><br><br>

    <button type="submit">Zaktualizuj</button>
</form>

<div id="responseUpdate"></div>
<h1>Aktualizuj zwierzę</h1>
<form id="petUForm">
    <label for="petId3">ID zwierzęcia:</label>
    <input type="text" id="petId3" name="petId3" required><br><br>

    <label for="name1">Nazwa zwierzęcia:</label>
    <input type="text" id="name1" name="name1" value="doggie" required><br><br>

    <label for="category">Kategoria:</label>
    <select id="category" name="category">
        <option value="16" selected>cat</option>
        <option value="17">dog</option>
        <option value="18">bird</option>
    </select><br><br>

    <label for="status1">Status:</label>
    <select id="status1" name="status1">
        <option value="available" selected>Dostępny</option>
        <option value="pending">Oczekujący</option>
        <option value="sold">Sprzedany</option>
    </select><br><br>

    <label for="tags">Tagi:</label>
    <select id="tags" name="tags">
        <option value="10" selected>firendly</option>
        <option value="11">shy</option>
        <option value="12">playful</option>
    </select><br><br>

    <button type="submit">Zaktualizuj</button>
</form>

<div id="responseU"></div>

<h1>Dodaj nowe zwierzę</h1>
<form id="petPostForm">

    <label for="name2">Nazwa zwierzęcia:</label>
    <input type="text" id="name2" name="name2" value="" required><br><br>

    <label for="category1">Kategoria:</label>
    <select id="category1" name="category1">
        <option value="16" selected>cat</option>
        <option value="17">dog</option>
        <option value="18">bird</option>
    </select><br><br>

    <label for="status2">Status:</label>
    <select id="status2" name="status2">
        <option value="available" selected>Dostępny</option>
        <option value="pending">Oczekujący</option>
        <option value="sold">Sprzedany</option>
    </select><br><br>

    <label for="tags1">Tagi:</label>
    <select id="tags1" name="tags1">
        <option value="10" selected>friendly</option>
        <option value="11">shy</option>
        <option value="12">playful</option>
    </select><br><br>

    <button type="submit">Dodaj nowe</button>
</form>

<div id="responsePost"></div>

<script>
    $(document).ready(function () {
        $('#petForm').on('submit', function (e) {
            e.preventDefault();
            var petId = $('#petId').val();
            $.ajax({
                url: `http://127.0.0.1:8000/api/pet/${petId}`,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    if (data) {
                        let tagsList = '';
                        if (data.tags && data.tags.length > 0) {
                            data.tags.forEach(tag => {
                                tagsList += `<span>${tag.name}</span> `;
                            });
                        } else {
                            tagsList = '<span>Brak tagów</span>';
                        }

                        $('#petData').html(`
                                <h2>Szczegóły zwierzęcia:</h2>
                                <p><strong>Imię:</strong> ${data.name}</p>
                                <p>Kategoria: ${data.category_id}</p>
                                <p>Status: ${data.status}</p>
                                <p>Tagi: ${tagsList}</p>
                            `);
                    } else {
                        $('#petData').html(`<p>Błąd 404: Pet not found.</p>`);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $('#petData').html(`<p>Błąd 404: Pet not found.</p>`);
                    } else if (xhr.status === 400) {
                        $('#petData').html(`<p>Błąd 400: Invalid ID supplied.</p>`);
                    } else {
                        $('#petData').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                    }
                }
            });
        });


        $('#statusForm').on('submit', function (e) {
            e.preventDefault();
            var selectedStatuses = '';
            $('input[name="status"]:checked').each(function () {
                if (selectedStatuses) {
                    selectedStatuses += ',';
                }
                selectedStatuses += $(this).val();
            });

            if (selectedStatuses) {
                var url = 'http://127.0.0.1:8000/api/pet/findByStatus?status=' + encodeURIComponent(selectedStatuses);
                console.log(url);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let petsList = '';
                        console.log(data);
                        if (data && data.length > 0) {
                            data.forEach(pet => {
                                let tagsList = '';
                                if (pet.tags && pet.tags.length > 0) {
                                    pet.tags.forEach(tag => {
                                        tagsList += `<span>${tag.name}</span> `;
                                    });
                                } else {
                                    tagsList = '<span>Brak tagów</span>';
                                }
                                petsList += `
                <div class="pet-details">
                    <h3>${pet.name}</h3>
                    <p><strong>Kategoria:</strong> ${pet.category_id}</p>
                    <p><strong>Status:</strong> ${pet.status}</p>
                    <p><strong>Tagi:</strong> ${tagsList}</p>
                </div>
            `;
                            });
                            $('#selectedStatuses').html(petsList);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 400) {
                            $('#selectedStatuses').html(`<p>Błąd 400: Invalid status value.</p>`);
                        } else {
                            $('#selectedStatuses').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                        }
                    }
                });
            } else {
                $('#selectedStatuses').html('<p>Nie wybrano żadnych statusów.</p>');
            }
        });


        $('#deleteForm').on('submit', function (e) {
            e.preventDefault();
            var petId = $('#delId').val();
            $.ajax({
                url: `http://127.0.0.1:8000/api/pet/${petId}`,
                type: 'DELETE',
                success: function (data) {
                    if (data) {
                        $('#delSt').html(`
                                <h2>Usunięto z bazy</h2>

                            `);
                    } else {
                        $('#delSt').html(`<p>Błąd 404: Pet not found.</p>`);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $('#delSt').html(`<p>Błąd 404: Pet not found.</p>`);
                    } else if (xhr.status === 400) {
                        $('#delSt').html(`<p>Błąd 400: Invalid ID supplied.</p>`);
                    } else {
                        $('#delSt').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                    }
                }
            });
        });

        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();
            var petId1 = $('#petId1').val();
            var formData = new FormData();
            formData.append('image', $('#image')[0].files[0]);
            $.ajax({
                url: `http://127.0.0.1:8000/api/pet/${petId1}/uploadImage`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#response').html('<p>Zdjęcie zostało przesłane pomyślnie!</p>');
                    console.log(response);
                },
                error: function (xhr, status, error) {
                    $('#response').html('<p>Wystąpił błąd: ' + status + " : " + error + '</p>');

                }
            });
        });

        $('#updateForm').on('submit', function (e) {
            e.preventDefault();
            var updatedData = {
                name: $('#name').val(),
                status: $('#status').val()
            };
            var petId2 = $('#petId2').val();
            $.ajax({
                url: `http://127.0.0.1:8000/api/pet/${petId2}`,
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(updatedData),
                success: function (response) {
                    $('#responseUpdate').html('<p>Zaktualizowano dane pomyślnie!</p>');
                    console.log(response);
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $('#responseUpdate').html(`<p>Błąd 404: Pet not found.</p>`);
                    } else if (xhr.status === 405) {
                        $('#responseUpdate').html(`<p>Błąd 405:Invalid input.</p>`);
                    } else {
                        $('#responseUpdate').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                    }
                }
            });
        });

        $('#petUForm').on('submit', function (e) {
            e.preventDefault();

            var petId3 = $('#petId3').val();
            var petData = {
                name: $('#name1').val(),
                category: {
                    id: $('#category').val(),
                    name: $('#category option:selected').text()
                },
                tags: [{id: $('#tags').val(), name: $('#tags  option:selected').val()}],
                status: $('#status1').val()
            };

            $.ajax({
                url: `http://127.0.0.1:8000/api/pet/${petId3}`,
                type: 'PUT',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(petData),
                success: function (response) {
                    $('#responseU').html(`<p>Zaktualizowano dane pomyślnie!</p>`);
                    console.log(response);
                },
                error: function (xhr) {
                    if (xhr.status === 404) {
                        $('#responseU').html(`<p>Błąd 404: Pet not found.</p>`);
                    } else if (xhr.status === 405) {
                        $('#responseU').html(`<p>Błąd 405:Invalid input.</p>`);
                    } else {
                        $('#responseU').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                    }
                }
            });
        });

        $('#petPostForm').on('submit', function (e) {
            e.preventDefault();

            var petData = {
                name: $('#name2').val(),
                category: {
                    id: $('#category1').val(),
                    name: $('#category1 option:selected').text()
                },
                tags: [{id: $('#tags1').val(), name: $('#tags1  option:selected').val()}],
                status: $('#status2').val()
            };

            $.ajax({
                url: `http://127.0.0.1:8000/api/pet`,
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(petData),
                success: function (response) {
                    $('#responsePost').html(`<p>Dodano nowe zwierzę pomyślnie!</p>`);
                    console.log(response);
                },
                error: function (xhr) {
                    if (xhr.status === 405) {
                        $('#responsePost').html(`<p>Błąd 405:Invalid input.</p>`);
                    } else {
                        $('#responsePost').html(`<p>Wystąpił nieoczekiwany błąd (Kod: ${xhr.status}). Spróbuj ponownie.</p>`);
                    }
                }
            });
        });

    });
</script>
</body>

</html>
