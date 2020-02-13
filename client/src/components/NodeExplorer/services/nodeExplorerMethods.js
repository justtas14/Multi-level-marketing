export default function getChildrenFromAddress(address, root) {
    if (address.length === 0) {
        return root;
    }
    if (root.id === address[0]) {
        return getChildrenFromAddress(address.slice(1), root);
    }
    return getChildrenFromAddress(address.slice(1), root.children[address[0]]);
}
export function getChildren(node) {
    return Object.getOwnPropertyNames(node.children).filter(key => (key !== '__ob__')).map(key => node.children[key]);
}
