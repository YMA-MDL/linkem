/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



ajaxUrl = "ajax/Ajax.php";
var Nodes;
var propertyList;
var nodeTemplateList;
var graphsSetMgr;
var allNodes = [];
var allEdges = [];


$(document).ready(function () {
    console.log("ready!");
    propertyList = new propertyList($("#nodeproperties").find(".panel-body"));
    nodeTemplateList = new nodeTemplateList($("#panelTemplates"));
    graphsSetMgr = new graphSet();
});

/*
 * UI EVENTS 
 */


$("#addGraphView").click(function () {
    graphsSetMgr.addGraphView();
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



var contextMenu = '<div id="graphCtxMenu" class="list-group"> ';
contextMenu+= '<a href="#" class="list-group-item list-group-item-warning" id="contextDetachElement"><span class="glyphicon glyphicon-minus" ></span> Detach</a>';
contextMenu+= '<a href="#" class="list-group-item list-group-item-danger" id="contextDeleteElement"><span class="glyphicon glyphicon-trash" ></span> Delete</a>';
contextMenu += '</div>';

var contextMenuItem =  '<a href="#" class="list-group-item list-group-item-info customContextItemMenu"></a>';

$("#createItemOnView").click(function () {
    // get select value
    var nodeTypeValue = $("#newItemTypeSelection").val();
    // get active graph 
    var activeGraph = getActiveGraph();
    // create the node in collection
    var newNode = new Node(nodeTemplateList.nodeTemplates[nodeTypeValue].name);

    activeGraph.nodeCollection.addNodeToCollection(newNode, true);
    // create in DB, and add to graph when success
    newNode.createInDb();
    newNode.addToGraph(activeGraph);
    activeGraph.selectNode(newNode.id);
});

$("#searchNodesByType").click(function () {
    
    // get nodeType value
    // Load modal content
    // open modal
    $('#searchByType').modal();
});

$("#deleteElt").click(function () {
    var ag = getActiveGraph();
    if (ag.nodeCollection.selectedNode !== null) {
        ag.nodeCollection.deleteNode(ag.nodeCollection.selectedNode);
    } else {
        ag.nodeCollection.deleteEdge(ag.nodeCollection.selectedEdge);
    }
});

$("#detachElt").click(function () {
    var ag = getActiveGraph();
    if (ag.nodeCollection.selectedNode !== null) {
        ag.nodeCollection.detachNode(ag.nodeCollection.selectedNode);
    } else {
        ag.nodeCollection.detachEdge(ag.nodeCollection.selectedEdge);
    }
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
        // find available relationships
        var activeGraph = getActiveGraph();
        var selectedSource = activeGraph.nodeCollection.nodes[$("#sourceItemSelection").val()];
        var selectedTarget = activeGraph.nodeCollection.nodes[$("#targetItemSelection").val()];
        var relationshipList = "";
        for (var i = 0; i < nodeTemplateList.edgeTemplates.length; i++) {
            if ((nodeTemplateList.edgeTemplates[i].source === selectedSource.type) && (nodeTemplateList.edgeTemplates[i].target === selectedTarget.type)) {
                relationshipList += "<option value='" + nodeTemplateList.edgeTemplates[i].name + "'>" + nodeTemplateList.edgeTemplates[i].name + "</option>";
            }
        }
        $("#relationshipNameList").html(relationshipList);
        // allow creating the relationship
        $("#relationshipAdd").removeClass("disabled");
    } else {
        $("#relationshipAdd").addClass("disabled");
        $("#relationshipNameList").html("");
    }
});

$("#relationshipAdd").click(function () {
    var activeGraph = getActiveGraph();
    activeGraph.nodeCollection.createRelationship($("#sourceItemSelection").val(), $("#targetItemSelection").val(), $("#relationshipNameList").val());
    $("#sourceItemSelection").val("");
    $("#targetItemSelection").val("");
    $("#relationshipName").val("");
    $(this).addClass("disabled");
});

$("#loadNodesByType").click(function () {
    var nodeType = nodeTemplateList.nodeTemplates[$("#openNodeTypeListView").val()].name;
    var newGraphView = new Graph();
    graphsSetMgr.openGraphList.push(newGraphView);
    setTimeout(function () {
        newGraphView.cy.ready(function () {
            newGraphView.loadViewByNodeType(nodeType);
        });
    }, 800);
});
$("#addNodesByType").click(function () {
    var nodeType = nodeTemplateList.nodeTemplates[$("#openNodeTypeListView").val()].name;
    var activeGraphView = getActiveGraph();
    activeGraphView.addToViewByNodeType(nodeType);
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

$("#nodeTemplateSelection").change(function () {
    if ($(this).val() !== "") {
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

$("#edgeTemplateSelection").change(function () {
    if ($(this).val() !== "") {
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
    for (var i = 0; i < graphsSetMgr.openGraphList.length; i++) {
        if (String(graphsSetMgr.openGraphList[i].DOMElementID) === $("#graphTabHeadersList").find("li.active").attr("graphId")) {
            console.log(graphsSetMgr.openGraphList[i].DOMElementID);
            return graphsSetMgr.openGraphList[i];
        }
    }
}

/*
 * [UI] propertylist Class
 */

function propertyList(DOMelement) {

    var that = this;

    this.DOMelement = DOMelement;

    this.switchPropertyTargetType = function (type) {
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
                } else if (activeGraph.nodeCollection.singleSelectionType === "edge") {
                    activeGraph.nodeCollection.selectedEdge.updateProperty(propName, propValue);
                    activeGraph.updateSelectedEltProperty(propName, propValue);
                }
            });
            $(".nodePropertyInput").addClass("activated");
        }
    };

    this.enableActions = function () {
        that.DOMelement.find(".eltActionsButtons").removeClass("disabled");
    };
    this.enableGroupActions = function () {
        that.DOMelement.find(".eltsActionsButtons").removeClass("disabled");
    };
    this.disableActions = function () {
        that.DOMelement.find(".eltActionsButtons").addClass("disabled");
    };
    this.disableGroupActions = function () {
        that.DOMelement.find(".eltsActionsButtons").addClass("disabled");
    };


}
;