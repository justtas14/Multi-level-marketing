import React from 'react';
import './PageBar.scss';

const PageBar = ( props ) => {

    const { pages, currentPage } = props;
    let numbers = [];
    for(let i = 1; i <= pages; i++){
        if (i == currentPage){
            numbers.push(
                <li className={"active"} key={i}><a disabled={true}>{i}</a></li>
            );
        } else {
            numbers.push(
                <li className={"waves-effect"} key={i}><a onClick={() => props.onClick(i)}>{i}</a></li>
            );
        }

    }
    return (
        <ul className="pagination">
            { numbers }
        </ul>
    );
};

export default PageBar;

