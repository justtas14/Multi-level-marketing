import React from 'react';
import './Modal.scss';
import Backdrop from './Backdrop';

const modal = (props) => (
    <React.Fragment>
        <Backdrop showModal={props.showModal} clicked={props.modalClosed}/>
        <div className="modal" 
            style={{transform: props.showModal ? 'translateY(0)' : 'translateY(-100vh)',
            opacity: props.showModal ? '1' : '0'
            }}>
            <iframe src={props.modal} />
    </div>
    </React.Fragment>
);

export default modal;

