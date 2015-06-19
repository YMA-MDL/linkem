/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var graphViews = [];
ajaxUrl = "ajax/Ajax.php";
var Nodes;
var propertyList;




$(document).ready(function () {
    console.log("ready!");
    propertyList = new propertyList($("#nodeproperties").find(".panel-body"));
});


/*
 * UI EVENTS 
 */



$("#addGraphView").click(function () {
    graphViews.push(new Graph());
});

$("#propertyAddButton").click(function () {
    var activeGraph = getActiveGraph();
    if (activeGraph.nodeCollection.singleSelection) {
        if (activeGraph.nodeCollection.singleSelectionType === "node") {
            var propName = prompt("Please enter the property name", "");
            if (propName != null) {
                propertyList.addPropertyField(propName, "");
                activeGraph.nodeCollection.selectedNode.properties[propName] = "";
            }
        } else {
            var propName = prompt("Please enter the property name", "");
            if (propName != null) {
                propertyList.addPropertyField(propName, "");
                activeGraph.nodeCollection.selectedEdge.properties[propName] = "";
            }
        }
    } else {
        standardNotification("select a node first", "warning");
    }
});

$("#createItemOnView").click(function () {
    // get select value
    var nodeTypeValue = $("#newItemTypeSelection").val();
    // get active graph 
    var activeGraph = getActiveGraph();
    // create the node in collection
    var newNode = new Node();
    activeGraph.nodeCollection.addNodeToCollection(newNode, true, nodeTypeValue);
    // create in DB, and add to graph when success
    newNode.createInDb();
    newNode.addToGraph(activeGraph);
    activeGraph.selectNode(newNode.id);
});

// disable typing
$('.notyping').keydown(function () {
    //code to not allow any changes to be made to input field
    return false;
});

$('.nodeSelection').focusin(function () {
    var field = $(this);
    var activeGraph = getActiveGraph();
    activeGraph.cy.once('tap', function (evt) {
        if (!(evt.cy == evt.cyTarget)) {
            field.val(evt.cyTarget.id());
            field.blur();
        } else {
            field.val("");
            field.blur();
        }
    });
});

$('.nodeSelection').focusout(function () {
    if (($("#sourceItemSelection").val() !== "") && ($("#targetItemSelection").val() !== "")) {
        $("#relationshipAdd").removeClass("disabled");
    } else {
        $("#relationshipAdd").addClass("disabled");
    }
});

$("#relationshipAdd").click(function () {
    var activeGraph = getActiveGraph();
    activeGraph.nodeCollection.createRelationship($("#sourceItemSelection").val(), $("#targetItemSelection").val(), $("#relationshipName").val());
    $("#sourceItemSelection").val("");
    $("#targetItemSelection").val("");
    $("#relationshipName").val("");
    $(this).addClass("disabled");
});

$("#loadNodesByType").click(function () {
    var nodeType = $("#openNodeTypeListView").val();
    var newGraphView = new Graph();
    graphViews.push(newGraphView);
    setTimeout(function () {
        newGraphView.cy.ready(function () {
            newGraphView.loadViewByNodeType(nodeType);
        });
    }, 800);
});

$("#expendEdgesUp").click(function () {
    var activeGraph = getActiveGraph();
    activeGraph.nodeCollection.loadRelationships(activeGraph, "up");
});
$("#expendEdgesDown").click(function () {
    var activeGraph = getActiveGraph();
    activeGraph.nodeCollection.loadRelationships(activeGraph, "down");
});
$("#expendEdgesUpAndDown").click(function () {
    var activeGraph = getActiveGraph();
    activeGraph.nodeCollection.loadRelationships(activeGraph, "both");
});

$("#nodeTemplateSelection").change(function(){
    if ($(this).val()!==""){
        $("#edgeTemplateSelection").val("");
        // display properties
        $.post(ajaxUrl, {
            prop: "getItemElements"
        }).success(function (data) {
            
        }).fail(function (err) {
            console.log(err);
        });
        // display image
    }
});

$("#edgeTemplateSelection").change(function(){
    if ($(this).val()!==""){
        $("#nodeTemplateSelection").val("");
        // display properties
        $.post(ajaxUrl, {
            prop: "getItemElements"
        }).success(function (data) {
            
        }).fail(function (err) {
            console.log(err);
        });
        // display image
    }
});




/*
 * Functions
 */

function getActiveGraph() {
    for (var i = 0; i < graphViews.length; i++) {
        if (String(graphViews[i].DOMElementID) === $("#graphTabHeadersList").find("li.active").attr("graphId")) {
            console.log(graphViews[i].DOMElementID);
            return graphViews[i];
        }
    }
}

/*
 * [UI] propertylist Class
 */

function propertyList(DOMelement) {

    var that = this;
    
    this.DOMelement = DOMelement;
    
    this.switchPropertyTargetType = function(type){
        $(".nodeOrEdgeTitle").html(type);
    };
    
    this.loadProperties = function (propArray) {
        $("#noSelectionWarning").addClass("hidden");
        $("#selectionPropertiesForm").removeClass("hidden");
        $("#selectionPropertiesForm").find("div.form-group:not(.fix)").detach();
        for (var k in propArray) {
            if (propArray.hasOwnProperty(k)) {
                that.addPropertyField(k, propArray[k]);
            }
        }
    };

    this.emptyMode = function () {
        $("#noSelectionWarning").removeClass("hidden");
        $("#selectionPropertiesForm").addClass("hidden");
    };

    this.addPropertyField = function (propName, propValue) {
        if ((propName !== "uniqueId") && (propName !== "type")) {
            var propertyLine = '  <div class="form-group"><label  class="col-sm-4 control-label">' + propName + '</label><div class="col-sm-8"><input type="text" class="nodePropertyInput form-control" propName="' + propName + '" value="' + propValue + '" ></div></div>';
            that.DOMelement.find("#propertyAddButtonGroup").before(propertyLine);

            $(".nodePropertyInput:not(.activated)").change(function () {
                var propName = $(this).attr("propName");
                var propValue = $(this).val();
                var activeGraph = getActiveGraph();
                if (activeGraph.nodeCollection.singleSelectionType === "node") {
                    activeGraph.nodeCollection.selectedNode.updateProperty(propName, propValue);
                    activeGraph.updateSelectedEltProperty(propName, propValue);
                }else if (activeGraph.nodeCollection.singleSelectionType === "edge"){
                    activeGraph.nodeCollection.selectedEdge.updateProperty(propName, propValue);
                    activeGraph.updateSelectedEltProperty(propName, propValue);
                }
            });
            $(".nodePropertyInput").addClass("activated");
        }
    };
}
;