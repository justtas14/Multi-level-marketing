import React from 'react';
import './PageBar.scss';

const PageBar = ( props ) => {

    const { pages, currentPage } = props;
    let numbers = [];
    for(let i = 1; i <= pages; i++){
        if (i == currentPage){
            numbers.push(
                <button disabled={true} key={i}>{i}</button>
            );
        } else {
            numbers.push(
                <button className={"selected"} key={i} onClick={() => props.onClick(i)}>{i}</button>
            );
        }

    }
    return (
        <div>
            { numbers }
        </div>
    );
};

export default PageBar;

