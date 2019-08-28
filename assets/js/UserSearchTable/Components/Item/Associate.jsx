import React, { Component } from 'react';
import './Associate.scss';

class Associate extends Component {
    constructor(props) {
        super(props);
        this.state = {
        };
        this.onClick = this.onClick.bind(this);
    }

    onClick() {
        this.props.mainAction(this.props.id);
    };

    render() {
        const d = new Date(this.props.date);
        const dateString = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getFullYear();
        return (
            <div className={"associate-container"}>
                <div className="associate-itemContainer" style={{'flex': 3}}>
                    {this.props.name}
                </div>
                <div className="associate-itemLevelContainer" style={{'flex': 1}}>
                    {this.props.level}
                </div>
                <div className="associate-itemContainer" style={{'flex': 3}}>
                    {this.props.email}
                </div>
                <div className="associate-itemContainer" style={{'flex': 2}}>
                    {this.props.phone || '-'}
                </div>
                <div className="associate-itemDateContainer" style={{'flex': 2}}>
                    {dateString}
                </div>
                <div className="associate-itemContainer" style={{'flex': 2}}>
                    <a onClick={this.onClick} className="btn">{this.props.mainActionLabel}</a>
                </div>
            </div>

        );
    }
}

export default Associate;
