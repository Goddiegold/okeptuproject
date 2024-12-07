@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Clinical Decision Support System Toolkit</h1>

    <div class="card">
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" id="symptom-search" class="form-control" placeholder="Search symptom" aria-label="Search symptom">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="add-symptom">Add</button>
                </div>
            </div>
            <ul id="selected-symptoms" class="list-group mb-3">
                <!-- Selected symptoms will appear here -->
            </ul>
            <button type="button" class="btn btn-primary" id="predict-illness">Predict Illness</button>
        </div>
    </div>
    <script>
document.getElementById('add-symptom').addEventListener('click', function() {
    let symptomInput = document.getElementById('symptom-search');
    let symptom = symptomInput.value.trim();

    if (symptom) {
        let symptomItem = document.createElement('li');
        symptomItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        symptomItem.innerHTML = `${symptom} <button type="button" class="btn btn-danger btn-sm remove-symptom">Remove</button>`;
        document.getElementById('selected-symptoms').appendChild(symptomItem);

        symptomInput.value = '';
        symptomInput.focus();
    }
});

document.getElementById('selected-symptoms').addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-symptom')) {
        event.target.parentElement.remove();
    }
});

document.getElementById('predict-illness').addEventListener('click', function() {
    let symptoms = [];
    document.querySelectorAll('#selected-symptoms .list-group-item').forEach(function(item) {
        symptoms.push(item.firstChild.textContent.trim());
    });

    if (symptoms.length < 3) {
        document.getElementById('result').innerHTML = `<h2>Please select at least 3 symptoms.</h2>`;
        return;
    }

    fetch("{{ route('cdss_toolkit.predict') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ symptoms: symptoms })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = `<h2>Predicted Illness: ${data.illness}</h2>`;
    })
    .catch(error => console.error('Error:', error));
});
</script>
    <div id="result"></div>
</div>
<div id="title">Medication Interaction Checker</div>
<div id="content">
    <form id="container" onsubmit="checkInteractions(event)" autocomplete="off"></form>
    <button class="button" type="button" onclick="addAnotherDrug()">Add another drug</button>
    <div id="interaction">
        <div class="interaction_title" id="serum_concentration_title" style="display:none;">Interactions affecting serum concentration:</div>
        <ul class="interaction_list" id="serum_concentration"></ul>
        <div class="interaction_title" id="metabolism_title" style="display:none;">Interactions affecting metabolism:</div>
        <ul class="interaction_list" id="metabolism"></ul>
        <div class="interaction_title" id="adverse_effects_title" style="display:none;">Interactions altering adverse effects:</div>
        <ul class="interaction_list" id="adverse_effects"></ul>
        <div class="interaction_title" id="therapeutic_efficacy_title" style="display:none;">Interactions altering therapeutic efficacy:</div>
        <ul class="interaction_list" id="therapeutic_efficacy"></ul>
        <div class="interaction_title" id="antihypertensive_title" style="display:none;">Interactions affecting hypotensive effects:</div>
        <ul class="interaction_list" id="antihypertensive"></ul>
        <div class="interaction_title" id="hypoglycemic_title" style="display:none;">Interactions affecting hypoglycemic effects:</div>
        <ul class="interaction_list" id="hypoglycemic"></ul>
        <div class="interaction_title" id="qt_title" style="display:none;">Interactions affecting the QT interval:</div>
        <ul class="interaction_list" id="qt"></ul>
        <div class="interaction_title" id="other_title" style="display:none;">Other interactions:</div>
        <ul class="interaction_list" id="other"></ul>
    </div>
    <div id="result"></div> <!-- Add a div for displaying results -->
</div>
<div class="card" id="clinical-guidelines">
            <a href="/clingud">Clinical Guidelines</a>
        </div>
<script src="{{ asset('medicine-interactions-checker-master/script.js') }}"></script>
<style>
body {
    background: url('{{ asset('images/3.jpg') }}') no-repeat center center fixed;
    background-size: cover;
    color: black;
    font-family: 'Nunito', sans-serif;
    font-weight: 200;
    height: 100vh;
    margin: 0;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.5);
    z-index: -1;
}

.navbar {
    background: linear-gradient(to right, skyblue, darkblue);
    padding: 1em;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 5em;
}

.navbar li {
    display: inline;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.card {
    background: rgba(255, 255, 255, 0.9); 
    border: 1px solid #ccc; 
    border-radius: 5px; 
    padding: 20px; 
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    width: 80%;
}

.input-group {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.input-group input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px 0 0 4px;
    border-right: none;
}

.input-group-append button {
    padding: 10px;
    border: 1px solid #ccc;
    border-left: none;
    border-radius: 0 4px 4px 0;
    background-color: cyan;
    color: black;
    cursor: pointer;
}

.input-group-append button:hover {
    background-color: #0CBAA6;
}

.list-group-item {
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid #ccc;
    margin-bottom: 5px;
    border-radius: 4px;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.list-group-item .btn-danger {
    background-color: red;
    border: none;
    color: dark grey;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.list-group-item .btn-danger:hover {
    background-color: darkred;
}

#predict-illness {
    display: block;
    width: 200px;
    height: 50px;
    background-color: cyan;
    color: black;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    margin: 20px auto;
}

#predict-illness:hover {
    background-color: #0CBAA6;
}

h1, h2 {
    text-align: center;
    color: black;
}

.card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    width: 80%;
}

#title {
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 20px;
}

#content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    align-self: center;
}

.button:hover {
    background-color: #0056b3;
}

.interaction_title {
    font-weight: bold;
    margin-top: 20px;
}

.interaction_list {
    list-style-type: none;
    padding: 0;
    margin: 10px 0;
        }

.interaction_list li {
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid #ccc;
    margin-bottom: 5px;
    border-radius: 4px;
    padding: 10px;
}

#result {
    text-align: center;
    margin-top: 20px;
}
#result {
    text-align: center;
    margin-top: 20px;
}

#clinical-guidelines {
            background: linear-gradient(to right, skyblue, green);
            opacity: 80%;
            color: black;
            width: 250px;
            height: 100px;
            margin: 4em;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        #clinical-guidelines:hover {
            transform: scale(1.05);
        }

        #clinical-guidelines a {
            text-decoration: none;
            color: black;
            font-size: 1.2em;
            position: relative;
        }
</style>
@endsection