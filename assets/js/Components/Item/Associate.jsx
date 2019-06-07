import React, { Component } from 'react';
import './Associate.scss';

class Associate extends Component {
    constructor(props) {
        super(props);
        this.state = {
        };
    }

    render() {
        return <div onClick={() => this.props.showModal(this.props.id)}
                className={"associate-container"}>
            <div className="associate-itemContainer">
                {this.props.name}
            </div>
            <div className="associate-itemLevelContainer">
                {this.props.level}
            </div>
            <div className="associate-itemContainer">
                {this.props.email}
            </div>
            <div className="associate-itemContainer">
                {this.props.phone || '-'}
            </div>
            <div className="associate-itemDateContainer">
                {this.props.date}
            </div>
             </div>;
    }
}

export default Associate;
