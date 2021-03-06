<script>
    var url = location.href; //pega endereço que esta no navegador
    url = url.split("/"); //quebra o endeço de acordo com a / (barra)
    var diagramUrl = 'http://'+url[2]+'/novo_bpmn/novo.bpmn';

    // var diagramUrl = 'https://cdn.rawgit.com/bpmn-io/bpmn-js-examples/dfceecba/starter/diagram.bpmn';

    // viewer instance
    var bpmnViewer = new BpmnJS({
        container: '#canvas'
    });


    /**
     * Open diagram in our viewer instance.
     *
     * @param {String} bpmnXML diagram to display
     */
    function openDiagram(bpmnXML) {

        // import diagram
        bpmnViewer.importXML(bpmnXML, function(err) {

            if (err) {
                return console.error('could not import BPMN 2.0 diagram', err);
            }

            // access viewer components
            var canvas = bpmnViewer.get('canvas');
            var overlays = bpmnViewer.get('overlays');


            // zoom to fit full viewport
            canvas.zoom('fit-viewport');

            // attach an overlay to a node
            overlays.add('SCAN_OK', 'note', {
                position: {
                    bottom: 0,
                    right: 0
                },
                html: '<div class="diagram-note">Mixed up the labels?</div>'
            });

            // add marker
            canvas.addMarker('SCAN_OK', 'needs-discussion');
        });
    }


    // load external diagram file via AJAX and open it
    $.get(diagramUrl, openDiagram, 'text');
</script>