ajaxUrl = "ajax/Ajax.php";


var graphStylesheet = [
    {
        selector: 'node',
        css: {
            'shape': 'roundrectangle',
            'content': 'data(brand)',
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
            'width': 10,
            'line-color': '#337ab7',
            'target-arrow-color': '#337ab7'
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

function Graph() {

    var that = this;
    // initialize
    this.viewName = "";
    this.cypher = "";
    this.DOMElementID = "";
    this.graphStyle = "";
    this.cy = {};
    this.nodeCollection = new nodeCollection();
    

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
        }
        var tabDomId = that.id;
        $("#addTabHeader").before('<li role="presentation" graphId="' + tabDomId + '" ><a href="#' + tabDomId + '" aria-controls="defaulttView" role="tab" data-toggle="tab">' + tabDomId + '</a></li>');
        $("#addTabContent").before('<div role="tabpanel" class="tab-pane" id="' + tabDomId + '"></div>');
        $("#" + tabDomId).load("modules/graphView.php");
        that.DOMElementID = that.id;
        setTimeout(function () {
            $('#graphTabHeadersList a[href="#' + tabDomId + '"]').tab('show');
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
            that.cy.on('tap', function (evt) {
                if (!(evt.cy == evt.cyTarget)) {
                    if (evt.cyTarget[0].group() === "nodes") {
                        that.nodeCollection.selectNode(evt.cyTarget.id());
                        $(".expendEdges").removeClass("disabled");
                    } else if (evt.cyTarget[0].group() === "edges") {
                        that.nodeCollection.selectEdge(evt.cyTarget.id());
                    }
                } else {
                    that.nodeCollection.unselectNodes();
                    $(".expendEdges").addClass("disabled");
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

    this.graphAddNode = function (type, id) {
        that.cy.add({
            group: "nodes",
            classes: type,
            data: {id: String(id), weight: 75},
            position: {x: 200, y: 200}
        });
        that.redrawGraph();
        that.nodeCollection.nodeIds.push(id);
        that.saveView();
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
            data: {id: String(edgeId), source: String(sourceId), target: String(targetId), weight: 75},
            position: {x: 200, y: 200}
        });
        that.redrawGraph();
        that.nodeCollection.edgesIds.push(edgeId);
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
            newNode.retrieveId();
            newNode.updateTemplateProperties();
            that.nodeCollection.nodes[nodes[i].uniqueId] = newNode;
            if (nodes[i].hasOwnProperty("uniqueId")) {
                var nodeData = {id: String(nodes[i].uniqueId), weight: 75};
                $.extend(nodeData, newNode.properties);
                nodeList.push({
                    group: "nodes",
                    classes: nodeType,
                    data: nodeData,
                    position: {x: 200, y: 200}
                });
                that.nodeCollection.nodeIds.push(String(nodes[i].uniqueId));
            }
        }
        for (var i = 0; i < edges.length; i++) {
            var newEdge = new Edge(edges[i].source, edges[i].target, edges[i].type, false);
            newEdge.properties = edges[i].data;
            newEdge.retrieveId();
            that.nodeCollection.edges[edges[i].uniqueId] = newEdge;
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
        console.log(selection);
        if (selection.length == 1) {
            selection[0].data(propName, propValue);
        }
    };
}