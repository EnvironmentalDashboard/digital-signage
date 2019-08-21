const position = { x: 0, y: 0 }
interact('.editable-template > .button').draggable({
    listeners: {
        start(event) {
            event.target.setAttribute('data-x', 0);
            event.target.setAttribute('data-y', 0);
        },
        move(event) {
            position.x += event.dx
            position.y += event.dy
            Object.assign(event.target.dataset, { x: position.x, y: position.y });

            event.target.style.transform =
                `translate(${position.x}px, ${position.y}px)`
        },
    }
}).resizable({
    edges: { left: true, right: true, bottom: true, top: true },
    modifiers: [
        // keep the edges inside the parent
        interact.modifiers.restrictEdges({
            outer: 'parent',
            endOnly: true
        })
    ],
    inertia: true,
    listeners: {
        start(event) {
            event.target.setAttribute('data-height', event.target.style.height);
            event.target.setAttribute('data-width', event.target.style.width);
            event.target.setAttribute('data-initial-height', event.target.clientHeight);
            event.target.setAttribute('data-initial-width', event.target.clientWidth);
        },
        move(event) {
            Object.assign(event.target.style, {
                width: `${event.rect.width}px`,
                height: `${event.rect.height}px`,
                transform: `translate(${event.deltaRect.left}px, ${event.deltaRect.top}px)`
            });
            Object.assign(event.target.dataset, { height: event.rect.height, width: event.rect.width });
        }
    }
});