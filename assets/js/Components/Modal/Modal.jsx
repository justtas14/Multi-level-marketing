import React from 'react';
import './Modal.scss';
import Backdrop from './Backdrop';
import Rick from '../../images/profile.jpg';

const modal = (props) => (
    <React.Fragment>
        <Backdrop showModal={props.showModal} clicked={props.modalClosed}/>
        <div className="modal" 
            style={{transform: props.showModal ? 'translateY(0)' : 'translateY(-100vh)',
            opacity: props.showModal ? '1' : '0'
            }}>
            <div className="modal-image">
                <img src={Rick} alt="modalImage"/>
            </div>
            <div className="modal-item">
                <strong>Name: </strong> {props.modal.name}
            </div>
            <div className="modal-item">
                <strong>Level: </strong> {props.modal.level}
            </div>
            <div className="modal-item">
                <strong>E-mail: </strong> {props.modal.email}
            </div>
            <div className="modal-item">
                <strong>Telephone Number: </strong>{props.modal.phone}
            </div>
            <div className="modal-item">
                <strong>Date of Enrolment: </strong>{props.modal.date}
            </div>
    </div>
    </React.Fragment>
);

export default modal;

