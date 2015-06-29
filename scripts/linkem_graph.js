ajaxUrl = "ajax/Ajax.php";


var graphStylesheet = [
    {
        selector: 'node',
        css: {
            'shape': 'roundrectangle',
            'content': 'data(label)',
            'text-valign': 'top',
            'text-halign': 'center',
            'text-transform': 'uppercase',
            'text-wrap': 'wrap',
            'height': 80,
            'width': 80,
            'background-fit': 'contain',
            'background-color': '#FFF',
            'border-color': '#000',
            'border-width': 1,
            'border-opacity': 1
        }
    }, {
        selector: 'edge',
        css: {
            'target-arrow-shape': 'triangle',
            'content': 'data(type)',
            'width': 12,
            'line-color': '#337ab7',
            'target-arrow-color': '#337ab7',
            'edge-text-rotation':'autorotate',
            'font-size': 20,
            'color': 'white',
            'text-outline-color':'black',
            'text-outline-width': 2
        }
    }, {
        selector: 'node:selected',
        css: {
            'border-width': 5,
            'border-color': '#00B',
            'height': 100,
            'width': 100
        }
    }, {
        selector: 'edge:selected',
        css: {
            'line-color': '#900',
            'target-arrow-color': '#900',
            'width': "12"
        }
    }
];

var styleDefinitions = [];
styleDefinitions['grid'] = {
    name: 'grid',
    fit: true, // whether to fit the viewport to the graph
    padding: 30, // padding used on fit
    boundingBox: undefined, // constrain layout bounds; { x1, y1, x2, y2 } or { x1, y1, w, h }
    avoidOverlap: true, // prevents node overlap, may overflow boundingBox if not enough space
    rows: undefined, // force num of rows in the grid
    columns: undefined, // force num of cols in the grid
    position: function (node) {
    }, // returns { row, col } for element
    sort: undefined, // a sorting function to order the nodes; e.g. function(a, b){ return a.data('weight') - b.data('weight') }
    animate: false, // whether to transition the node positions
    animationDuration: 500, // duration of animation in ms if enabled
    ready: undefined, // callback on layoutready
    stop: undefined // callback on layoutstop
};

styleDefinitions['circle'] = {
    name: 'circle',
    fit: true, // whether to fit the viewport to the graph
    padding: 30, // the padding on fit
    boundingBox: undefined, // constrain layout bounds; { x1, y1, x2, y2 } or { x1, y1, w, h }
    avoidOverlap: true, // prevents node overlap, may overflow boundingBox and radius if not enough space
    radius: undefined, // the radius of the circle
    startAngle: 3 / 2 * Math.PI, // the position of the first node
    counterclockwise: false, // whether the layout should go counterclockwise (true) or clockwise (false)
    sort: undefined, // a sorting function to order the nodes; e.g. function(a, b){ return a.data('weight') - b.data('weight') }
    animate: false, // whether to transition the node positions
    animationDuration: 500, // duration of animation in ms if enabled
    ready: undefined, // callback on layoutready
    stop: undefined // callback on layoutstop
};

styleDefinitions['arbor'] = {
    name: 'arbor',
    animate: true, // whether to show the layout as it's running
    maxSimulationTime: 4000, // max length in ms to run the layout
    fit: true, // on every layout reposition of nodes, fit the viewport
    padding: 30, // padding around the simulation
    boundingBox: undefined, // constrain layout bounds; { x1, y1, x2, y2 } or { x1, y1, w, h }
    ungrabifyWhileSimulating: false, // so you can't drag nodes during layout

    // callbacks on layout events
    ready: undefined, // callback on layoutready
    stop: undefined, // callback on layoutstop

    // forces used by arbor (use arbor default on undefined)
    repulsion: undefined,
    stiffness: undefined,
    friction: undefined,
    gravity: true,
    fps: undefined,
    precision: undefined,
    // static numbers or functions that dynamically return what these
    // values should be for each element
    // e.g. nodeMass: function(n){ return n.data('weight') }
    nodeMass: undefined,
    edgeLength: undefined,
    stepSize: 0.1, // smoothing of arbor bounding box

    // function that returns true if the system is stable to indicate
    // that the layout can be stopped
    stableEnergy: function (energy) {
        var e = energy;
        return (e.max <= 0.5) || (e.mean <= 0.3);
    },
    // infinite layout options
    infinite: false // overrides all other options for a forces-all-the-time mode
};


/*
 * Graph Class
 */

function updateGraphStyleSheetImages() {
    //cleanNodeTypes
    for (var i = 0; i < graphStylesheet.length; i++) {
        if (graphStylesheet[i].type === 'nodeType') {
            graphStylesheet.splice(i, 1);
        }
    }
    for (var i = 0; i < nodeTemplateList.nodeTemplates.length; i++) {
        graphStylesheet.push({
            type: 'nodeType',
            selector: '.' + nodeTemplateList.nodeTemplates[i].name,
            css: {
                'background-image': nodeTemplateList.nodeTemplates[i].image
            }
        });
    }
}



function graphSet() {

    // properties

    var that = this;

    this.openGraphList = [];

    this.savedViews = [];

    // functions

    this.getSavedViews = function () {
        $.post(ajaxUrl, {
            action: "getViews"
        }).success(function (data) {
            var viewListContent = "";
            that.savedViews = [];
            for (var i = 0; i < data.content.length; i++) {
                that.savedViews.push(data.content[i]);
                var listItemName = "";
                if (data.content[i].name === null) {
                    listItemName = data.content[i].id;
                } else {
                    listItemName = data.content[i].name;
                }
                viewListContent += "<option value='" + i + "'>" + listItemName + "</option>";
            }
            $("#savedViewsList").html(viewListContent);
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.updateGraphItemLabels = function (id,label){
        for (var i = 0; i < that.openGraphList.length; i++) {
            that.openGraphList[i].cy.getElementById(id).data('label', label);
        }
    };

    this.openSelectedSavedView = function (graphViewObject) {
        $.post(ajaxUrl, {
            action: "runQuery",
            query: graphViewObject.query
        }).success(function (data) {
            console.log(data);
        }).fail(function (err) {
            console.log(err);
        });
    };
    this.deleteSelectedSavedView = function (viewUid) {
        $.post(ajaxUrl, {
            action: "deleteSavedView",
            viewUniqueId: viewUid
        }).success(function (data) {
            // delete view
            var listindex = -1;
            for(var i=0;i<that.savedViews.length;i++){
                if (that.savedViews[i].uid===viewUid){
                    that.savedViews.splice(i, 1);
                    listindex = i;
                }
            }
            // update view list
            $("#savedViewsList option:eq("+listindex+")").remove();

        }).fail(function (err) {
            console.log(err);
        });
    };

    this.closeView = function (tabId) {
        console.log("close :" + tabId);
        $(".tab-pane#" + tabId).remove();
        $("li[graphid='" + tabId + "']").remove();
    };

    this.updateViewName = function () {

    };

    this.addGraphView = function () {
        var newTab = new Graph();
        that.openGraphList.push(newTab);
        that.enableGraphTabButtons(newTab);
    };

    this.enableGraphTabButtons = function (newTab) {
        newTab.TabDOMElement.find(".closeTab").click(function () {
            var clickedTabId = $(this).closest("li").attr("graphId");
            // save  view
            // close view
            that.closeView(clickedTabId);
        });
        newTab.TabDOMElement.find(".trashTab").click(function () {
            var graphViewObjectUID = $(this).closest("li").attr("graphId");
            // delete view
            that.deleteSelectedSavedView(graphViewObjectUID);
            // close view
            that.closeView(graphViewObjectUID);
        });
        newTab.TabDOMElement.find("a").dblclick(function () {
            var clickedTabId = $(this).closest("li").attr("graphId");
            var tabName = prompt("Please enter the node template name", $(this).text());
            if (tabName != null) {
                that.updateViewName(clickedTabId);
            }
        });
    };

    // UI events
    $("#loadSavedView").click(function () {
        var graphViewObject = that.savedViews[$("#savedViewsList").val()];
        that.openSelectedSavedView(graphViewObject);
    });

    $("#deleteSavedView").click(function () {
        var graphViewObject = that.savedViews[$("#savedViewsList").val()];
        that.deleteSelectedSavedView(graphViewObject.uid);
        that.getSavedViews();
    });



    // init
    this.getSavedViews();
}




function Graph() {

    // properties
    var that = this;

    this.gridViews = new viewGrids();

    this.graphOrGrid = "graph";

    this.viewName = "";

    this.cypher = "";

    this.DOMElementID = "";

    this.DOMElement;

    this.TabDOMElement;

    this.graphStyle = "";

    this.cy = {};

    this.savedViews = [];

    this.nodeCollection = new nodeCollection();

    this.connectionProcess = {
        source: null,
        target: null,
        targetTypes: [],
        type: "",
        status: "idle"
    };

    // functions


    this.loadView = function (viewId) {
        that.id = viewId;
        $.post(ajaxUrl, {
            action: "getView",
            viewId: viewId
        }).success(function (data) {
            this.id = data.content.graphId;
            this.cypher = data.content.graphcypher;
            this.graphStyle = data.content.graphStyle;
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.loadViewByNodeType = function (nodeType) {
        // load nodeCollection
        that.nodeCollection.loadNodeTypeCollection(nodeType, that);

    };
    this.addToViewByNodeType = function (nodeType) {
        // load nodeCollection
        that.nodeCollection.loadNodeTypeCollection(nodeType, that);

    };
    this.createViewInDb = function () {
        that.id = Date.now();
        $.post(ajaxUrl, {
            action: "createView",
            viewId: that.id
        }).success(function (data) {
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.saveView = function () {
        var viewState = {};
        viewState["nodes"] = that.nodeCollection.nodeIds;
        viewState["edges"] = that.nodeCollection.edgesIds;
        $.post(ajaxUrl, {
            action: "saveViewState",
            viewId: that.id,
            viewState: viewState
        }).success(function (data) {
            graphsSetMgr.getSavedViews();
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.saveQuery = function (viewId) {
        $.post(ajaxUrl, {
            action: "saveViewQuery",
            viewId: viewId
        }).success(function (data) {
            this.id = data.content.graphId;
            this.cypher = data.content.graphcypher;
            this.graphStyle = data.content.graphStyle;
        }).fail(function (err) {
            console.log(err);
        });
    };

    this.addGraphTab = function (isNew) {
        if (isNew) {
            that.createViewInDb();
            graphsSetMgr.getSavedViews();
        }
        var tabDomId = that.id;
        $("#addTabHeader").before('<li role="presentation" graphId="' + tabDomId + '" >\n\
<a href="#' + tabDomId + '" tabid="'+tabDomId+'" aria-controls="defaulttView" role="tab" data-toggle="tab">' + tabDomId + '\
<span class="glyphicon glyphicon-remove tabActions closeTab" ></span>\n\
<span class="glyphicon glyphicon-trash tabActions trashTab" ></span></a></li>');
        $("#addTabContent").before('<div role="tabpanel" class="tab-pane" id="' + tabDomId + '"></div>');
        $("#" + tabDomId).load("modules/graphView.php");
        that.DOMElementID = that.id;
        that.DOMElement = $("#" + tabDomId);
        that.TabDOMElement = $("[graphId='" + tabDomId + "']");
        setTimeout(function () {
            $('#graphTabHeadersList a[href="#' + tabDomId + '"]').tab('show');
            that.DOMElement.children(".switchGridGraph").click(function () {
                $(this).toggleClass("graphState");
                $(this).toggleClass("gridState");
                that.DOMElement.children(".graphView").toggleClass("hidden");
                that.DOMElement.children(".gridView").toggleClass("hidden");
                //that.gridViews.empty();
                if (that.graphOrGrid === "graph") {
                    that.graphOrGrid === "grid";
                    that.gridViews.load(that.nodeCollection, that.DOMElement.children(".gridView"));
                }
            });
            that.DOMElement.children(".graphControl.fit").click(function () {
                that.cy.fit();
            });
            that.DOMElement.children(".graphControl.center").click(function () {
                that.cy.center();
            });
        }, 500);
        $("#createItemOnView").removeClass("disabled");
        $("#searchNodesByType").removeClass("disabled");
    };

    this.buildCytoscapeGraph = function (style) {
        if (this.cypher !== "") {
            $.post(ajaxUrl, {
                action: "getCypherGraph",
                cypher: this.cypher
            }).success(function (data) {
                $("#" + this.DOMElementID).children(".graphView").cytoscape({
                    elements: {
                        nodes: data.content.nodes,
                        edges: data.content.edges
                    },
                    layout: this.graphStyle,
                    style: graphStylesheet
                });
                this.cy = $("#" + this.DOMElementID).children(".graphView").cytoscape('get');

            }).fail(function (err) {
                console.log(err);
            });
        } else {
            $("#" + this.DOMElementID).children(".graphView").cytoscape({
                elements: {
                    nodes: [],
                    edges: []
                },
                layout: this.graphStyle,
                style: graphStylesheet
            });
            that.cy = $("#" + this.DOMElementID).children(".graphView").cytoscape('get');
            that.cy.on('select', function (evt) {
                if (!(isKeyPressed(event))) {
                    if (that.cy.$(":selected").size() === 1) {
                        if (evt.cyTarget[0].group() === "nodes") {
                            that.nodeCollection.selectNode(evt.cyTarget.id());
                            $(".expendEdges").removeClass("disabled");
                            if (that.connectionProcess.status === "processing") {
                                if (that.connectionProcess.targetTypes.indexOf(that.nodeCollection.selectedNode.type) > -1) {
                                    that.connectionProcess.target = evt.cyTarget.id();
                                    that.nodeCollection.createRelationship(that.connectionProcess.source, that.connectionProcess.target, that.connectionProcess.type);
                                    $("#sourceItemSelection").val("");
                                    $("#targetItemSelection").val("");
                                    $("#relationshipName").val("");
                                    $(this).addClass("disabled");
                                    that.connectionProcess.status = "idle";
                                } else {
                                    $("#sourceItemSelection").val("");
                                    $("#targetItemSelection").val("");
                                    $("#relationshipName").val("");
                                    $(this).addClass("disabled");
                                    that.connectionProcess.status = "idle";
                                    standardNotification("relationship Not allowed", "warning");
                                }
                            }
                        } else if (evt.cyTarget[0].group() === "edges") {
                            that.nodeCollection.selectEdge(evt.cyTarget.id());
                        }
                    } else {
                        that.nodeCollection.selectGroupedNodes();
                        //that.nodeCollection.selectCollectionNode(that.cy.$(":selected"));
                    }
                } else {
                    console.log("accumulate");
                }
            });
            that.cy.on('tap', function (evt) {
                if ((evt.cy == evt.cyTarget)) {
                    that.nodeCollection.unselectNodes();
                    $(".expendEdges").addClass("disabled");
                    that.connectionProcess.status = "idle";

                }
                if ($("#graphCtxMenu").length > 0) {
                    $("#graphCtxMenu").remove();
                }
            });


            that.cy.on('cxttapend ', function (evt) {
                if (!(evt.cy == evt.cyTarget)) {
                    // handle contextMenu

                    var currentMousePos = {};
                    currentMousePos.x = event.pageX;
                    currentMousePos.y = event.pageY;
                    // create the context menu if it does not exist
                    if ($("#graphCtxMenu").length < 1) {
                        $(".centralpanel").append(contextMenu);
                    }
                    //fill the context menu with available relationships
                    $(".customContextItemMenu").remove();

                    if ($("#contextGetElementLinks").length < 1) {
                        $("#graphCtxMenu").prepend(contextLinkItem);
                    }
                    if (evt.cyTarget[0].group() === "nodes") {
                        that.nodeCollection.selectNode(evt.cyTarget.id());
                        for (var i = 0; i < nodeTemplateList.edgeTemplates.length; i++) {
                            if (nodeTemplateList.edgeTemplates[i].source.indexOf(that.nodeCollection.selectedNode.type) > -1) {
                                $("#graphCtxMenu").prepend(contextMenuItem);
                                $("#graphCtxMenu").find(".customContextItemMenu:first()").html("connect to " + nodeTemplateList.edgeTemplates[i].target);
                                $("#graphCtxMenu").find(".customContextItemMenu:first()").attr("title", "via " + nodeTemplateList.edgeTemplates[i].name);
                                $("#graphCtxMenu").find(".customContextItemMenu:first()").attr("linkType", nodeTemplateList.edgeTemplates[i].name);
                                $("#graphCtxMenu").find(".customContextItemMenu:first()").attr("target", nodeTemplateList.edgeTemplates[i].target);
                                $("#graphCtxMenu").find(".customContextItemMenu:first()").attr("linkem_role", "connectTo");
                                that.connectionProcess.targetTypes = nodeTemplateList.edgeTemplates[i].target;
                            }
                        }
                        $("[linkem_role='connectTo']").click(function () {
                            console.log("start connection");
                            that.connectionProcess.source = evt.cyTarget.id();
                            $("#sourceItemSelection").val(evt.cyTarget.id());
                            that.connectionProcess.status = "processing";
                            that.connectionProcess.type = $(this).attr("linkType");

                            standardNotification("select a single node to build the relationship", "info");
                            $("#graphCtxMenu").remove();
                        });
                        $("#contextGetElementLinks").click(function () {
                            that.nodeCollection.loadRelationships(that, "both");
                            $("#graphCtxMenu").remove();
                        });
                        $("#contextDetachElement").click(function () {
                            if (that.nodeCollection.selectedNode !== null) {
                                that.nodeCollection.detachNode(that.nodeCollection.selectedNode);
                            } else {
                                that.nodeCollection.detachEdge(that.nodeCollection.selectedEdge);
                            }
                            $("#graphCtxMenu").remove();
                        });
                        $("#contextDeleteElement").click(function () {
                            if (that.nodeCollection.selectedNode !== null) {
                                that.nodeCollection.deleteNode(that.nodeCollection.selectedNode);
                            } else {
                                that.nodeCollection.deleteEdge(that.nodeCollection.selectedEdge);
                            }
                            $("#graphCtxMenu").remove();
                        });
                        $("#graphCtxMenu").css("left", currentMousePos.x);
                        $("#graphCtxMenu").css("top", currentMousePos.y);
                        $('#graphCtxMenu').dropdown();
                    }

                    if (evt.cyTarget[0].group() === "edges") {
                        that.nodeCollection.selectEdge(evt.cyTarget.id());

                        $("#contextDetachElement").click(function () {
                            if (that.nodeCollection.selectedEdge !== null) {
                                that.nodeCollection.detachEdge(that.nodeCollection.selectedEdge);
                            }
                            $("#graphCtxMenu").remove();
                        });
                        $("#contextDeleteElement").click(function () {
                            if (that.nodeCollection.selectedEdge !== null) {
                                that.nodeCollection.deleteEdge(that.nodeCollection.selectedEdge);
                            }
                            $("#graphCtxMenu").remove();
                        });
                        $("#graphCtxMenu").css("left", currentMousePos.x);
                        $("#graphCtxMenu").css("top", currentMousePos.y);
                        $('#graphCtxMenu').dropdown();
                    }


                } else {
                    if ($("#graphCtxMenu").length > 0) {
                        $("#graphCtxMenu").remove();
                    }
                }
            });
        }
    };

    this.reloadGraphContent = function () {

    };

    this.selectNode = function (nodeId) {
        var graphElement = that.cy.getElementById(nodeId);
        that.cy.center(graphElement);
        that.cy.$("*").unselect();
        graphElement.select();
    };

    this.graphAddNode = function (type, id,label) {
        that.cy.add({
            group: "nodes",
            classes: type,
            data: {id: String(id), weight: 75,label:label},
            position: {x: 200, y: 200}
        });
        that.redrawGraph();
        that.saveView();
        graphsSetMgr.getSavedViews();
    };

    this.graphAddNodeList = function (nodeList) {
        that.cy.add(nodeList);
        that.cy.layout({name: "cose"});
        that.redrawGraph();
        that.saveView();
    };

    this.graphAddEdge = function (sourceId, targetId, edgeId, type) {
        that.cy.add({
            group: "edges",
            classes: type,
            data: {id: String(edgeId), type: type, source: String(sourceId), target: String(targetId), weight: 75},
            position: {x: 200, y: 200}
        });
        that.redrawGraph();
        that.saveView();
    };

    this.redrawGraph = function () {
        that.cy.resize();
    };

    this.changeStyle = function (style) {
        that.cy.layout(styleDefinitions[style]);

    };

    // this.loadView();
    this.addGraphTab(true);
    setTimeout(function () {
        that.buildCytoscapeGraph("circle");
    }, 600);


    this.displayNodeTypeList = function (nodes, edges) {
        var nodeList = [];
        for (var i = 0; i < nodes.length; i++) {
            var nodeType = nodes[i]["type"];
            var newNode = new Node(nodeType);
            newNode.properties = nodes[i];
            newNode.setLabel();
            newNode.retrieveId();
            newNode.updateTemplateProperties();
            that.nodeCollection.nodes[newNode.id] = newNode;
            allNodes[newNode.id] = newNode;
            if (nodes[i].hasOwnProperty("uniqueId")) {
                var nodeData = {id: String(newNode.id), weight: 75,label:newNode.label};
                $.extend(nodeData, newNode.properties);
                nodeList.push({
                    group: "nodes",
                    classes: nodeType,
                    data: nodeData,
                    position: {x: 200, y: 200},

                });
                that.nodeCollection.nodeIds.push(String(newNode.id));
            }
        }
        for (var i = 0; i < edges.length; i++) {
            var newEdge = new Edge(edges[i].source, edges[i].target, edges[i].type, false);
            newEdge.properties = edges[i].data;
            newEdge.retrieveId();
            that.nodeCollection.edges[edges[i].uniqueId] = newEdge;
            allEdges[edges[i].uniqueId] = newEdge;
            if (edges[i].hasOwnProperty("uniqueId")) {

                var edgeData = {
                    id: String(edges[i].uniqueId),
                    source: String(edges[i].source),
                    target: String(edges[i].target),
                    type: String(edges[i].type)
                };
                $.extend(edgeData, newEdge.properties);
                nodeList.push({
                    group: "edges",
                    data: edgeData
                });
                that.nodeCollection.edgeIds.push(String(edges[i].uniqueId));
            }
        }
        that.graphAddNodeList(nodeList);
    };

    this.startNewViewFromNodeSelection = function () {

    };

    this.addSelectedNodesToOtherView = function () {

    };

    this.updateSelectedEltProperty = function (propName, propValue) {
        var selection = that.cy.$(":selected");
        if (selection.length == 1) {
            selection[0].data(propName, propValue);
        }
    };

    // events
}


function viewGrids() {
    var that = this;

    this.grids = {};

    this.load = function (nodeCollection, viewDOM) {
        viewDOM.find("ul.nav").empty();
        viewDOM.find("div.tab-content").empty();
        var nodeTypes = [];
        // sort the nodeCollection Data
        for (var k in nodeCollection.nodes) {
            if (nodeTypes.indexOf(nodeCollection.nodes[k].type) < 0) {
                nodeTypes.push(nodeCollection.nodes[k].type);
            }
        }

        // build Divs and Grids
        for (var i = 0; i < nodeTypes.length; i++) {
            // build Grid
            that.grids[nodeTypes[i]] = new grid(nodeTypes[i]);
            // add the grid div
            if (i === 0) {
                viewDOM.find("ul.nav").append('<li role="presentation" class="active"><a href="#' + nodeTypes[i] + '" aria-controls="home" role="tab" data-toggle="tab">' + nodeTypes[i] + '</a></li>');
                viewDOM.find("div.tab-content").append('<div role="tabpanel" class="tab-pane active gridTab" id="' + nodeTypes[i] + '"></div>');
            } else {
                viewDOM.find("ul.nav").append('<li role="presentation" ><a href="#' + nodeTypes[i] + '" aria-controls="home" role="tab" data-toggle="tab">' + nodeTypes[i] + '</a></li>');
                viewDOM.find("div.tab-content").append('<div role="tabpanel" class="tab-pane gridTab" id="' + nodeTypes[i] + '"></div>');
            }
            viewDOM.find("div#" + nodeTypes[i]).append("<table class='table table-striped table-bordered' id='" + nodeTypes[i] + "'><thead><tr></tr></thead><tfoot><tr></tr></tfoot></table>");
            for (var j = 0; j < that.grids[nodeTypes[i]].columns.length; j++) {
                viewDOM.find("table#" + nodeTypes[i] + " thead tr").append("<th>" + that.grids[nodeTypes[i]].columns[j]['data'] + "</th>");
                viewDOM.find("table#" + nodeTypes[i] + " tfoot tr").append("<th>" + that.grids[nodeTypes[i]].columns[j]['data'] + "</th>");
            }

            // build content
            for (var k in nodeCollection.nodes) {
                var dataRow = {};
                if (nodeCollection.nodes[k].type === nodeTypes[i]) {

                    for (var j = 0; j < that.grids[nodeTypes[i]].columns.length; j++) {
                        if (dataRow[nodeTypes[i]] === undefined) {
                            dataRow[nodeTypes[i]] = {};
                        }
                        if (nodeCollection.nodes[k].properties[that.grids[nodeTypes[i]].columns[j]['data']] !== undefined) {
                            dataRow[nodeTypes[i]][that.grids[nodeTypes[i]].columns[j]['data']] = nodeCollection.nodes[k].properties[that.grids[nodeTypes[i]].columns[j]['data']];
                        } else {
                            dataRow[nodeTypes[i]][that.grids[nodeTypes[i]].columns[j]['data']] = "";
                        }
                    }
                    that.grids[nodeTypes[i]].data.push(dataRow[nodeTypes[i]]);
                }
            }
            // start the datatable
            console.log(that.grids[nodeTypes[i]].columns.length);
            if (that.grids[nodeTypes[i]].columns.length>0){
                viewDOM.find("table#" + nodeTypes[i]).DataTable({
                    data: that.grids[nodeTypes[i]].data,
                    columns: that.grids[nodeTypes[i]].columns
                });
            }
        }
    };
}

function grid(nodeType) {

    // properties
    var that = this;

    this.columns = [];
    this.data = [];
    this.columnsDataType = [];

    // functions
    this.getColumns = function (nodeType) {
        for (var i = 0; i < nodeTemplateList.nodeTemplates.length; i++) {
            if (nodeTemplateList.nodeTemplates[i].name === nodeType) {
                for (var key in nodeTemplateList.nodeTemplates[i].properties) {
                    if (!(key.startsWith("_"))) {
                        var column = {};
                        column['data'] = key;
                        that.columns.push(column);
                        that.columnsDataType.push(nodeTemplateList.nodeTemplates[i].properties[key]);
                    }
                }
            }
        }
    };



    // events

    // startup
    /// build columns on Load
    this.getColumns(nodeType);

}
