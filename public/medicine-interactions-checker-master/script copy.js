function queryInteractions() {
    var rxcuis = "";
    var titles = document.getElementsByClassName("interaction_title");
    for (var title = 0; title < titles.length; title++) {
        titles[title].style.display = "none";
    }
    var lists = document.getElementsByClassName("interaction_list");
    for (var list = 0; list < lists.length; list++) {
        lists[list].innerHTML = "";
    }
    for (var drug in drugArray) {
        rxcuis += drugArray[drug] + "+";
    }
    rxcuis = rxcuis.slice(0, -1);
    $.ajax({ 
        type: "GET",
        dataType: "json",
        url: "https://rxnav.nlm.nih.gov/REST/interaction/list.json?sources=DrugBank&rxcuis=" + rxcuis,
        success: function(data){        
            analyseInteractions(data);
        }
    });
}

function analyseInteractions(data) {
    var licenceText = data.nlmDisclaimer;
    var interactionList = [];
    var effect = "";
    var tuple = [];

    if (data.fullInteractionTypeGroup) {
        for (var source in data.fullInteractionTypeGroup) {
            licenceText += "<br/>Drug interaction information is provided by DrugBank. " + data.fullInteractionTypeGroup[source].sourceDisclaimer.replace(".[www.drugbank.ca]", ".");
            for (var i in data.fullInteractionTypeGroup[source].fullInteractionType) {
                for (var interactionPair in data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair) {
                    for (var j in data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].interactionConcept) {
                        if (data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].interactionConcept[j].minConceptItem.name) {
                            if (data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].interactionConcept[j].minConceptItem.rxcui != 0) {
                                if (!interactionList.includes(data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].description)) {
                                    interactionList.push(data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].description);
                                }
                                tuple.push(data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].description);
                                tuple.push(data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].interactionConcept[j].minConceptItem.name);
                                tuple.push(data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].severity);
                                if (data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].severity == "high") {
                                    effect = "dangerous interaction";
                                }
                                else if (data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].severity == "moderate") {
                                    effect = "possible interaction";
                                }
                                else if (data.fullInteractionTypeGroup[source].fullInteractionType[i].interactionPair[interactionPair].severity == "low") {
                                    effect = "interaction possible but not likely to be of clinical significance";
                                }
                                else {
                                    effect = "No known interaction";
                                }
                                tuple.push(effect);
                                displayResult(tuple); // Pass tuple to displayResult function
                                tuple = [];
                            }
                        }
                    }
                }
            }
        }
    }

    if (interactionList.length === 0) {
        document.getElementById("licence").innerHTML = "No interactions found";
    } else {
        document.getElementById("licence").innerHTML = licenceText;
    }
}

function checkInteractions(event) {
    event.preventDefault(); // Prevent the form from submitting normally
    queryInteractions();
}

function displayResult(result) {
    // Extract drug names and interaction severity from the result tuple
    var drug1 = result[1];
    var drug2 = result[2];
    var interactionSeverity = result[3];

    // Construct the result message
    var resultMessage = `${drug1} and ${drug2} have a ${interactionSeverity}`;

    // Create a new div element for the result message
    var resultDiv = document.createElement("div");
    resultDiv.textContent = resultMessage;

    // Append the result message to the result div
    document.getElementById("result").appendChild(resultDiv);
}