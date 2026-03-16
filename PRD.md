AI-Powered Travel Booking & Itinerary Planner
With Demo Payment Gateway
Academic Project – MCA Semester 2
1. Introduction

This document defines the complete Product Requirements for the AI-Powered Travel Booking & Itinerary Planner, a web-based full-stack system that simulates a real-world travel booking platform integrated with Artificial Intelligence for itinerary generation and a demo payment gateway to simulate transaction workflows.

The system combines:

Flight & Hotel booking simulation

AI-based itinerary generation

Payment lifecycle simulation

Admin management dashboard

Secure authentication & validation

2. Problem Statement

Modern travel booking systems focus mainly on reservations and lack:

Personalized AI travel planning

Budget breakdown visibility

Day-wise structured itineraries

Intelligent destination recommendations

Users struggle with:

Planning daily schedules

Budget allocation

Selecting suitable hotels

Understanding cost distribution

This project solves these problems by integrating AI-powered planning with booking simulation.

3. Objectives

Develop a responsive full-stack travel booking platform

Implement AI-based itinerary generation

Simulate a real-world payment lifecycle

Design a structured relational database

Implement secure authentication & validation

Provide admin management panel

Demonstrate modular and scalable architecture

4. Project Scope
4.1 In Scope

User Authentication System

Flight Search & Booking (Mock Data)

Hotel Search & Booking (Mock Data)

AI Itinerary Generation

Demo Payment Gateway

Booking Confirmation System

Admin Dashboard

Sorting & Filtering

Booking History

Itinerary Save Feature

4.2 Out of Scope

Real airline APIs

Real hotel APIs

Live payment gateway

Third-party travel APIs

Production deployment

5. System Architecture
5.1 Architecture Overview
1️⃣ Presentation Layer

HTML5

CSS3

Bootstrap or Tailwind CSS

JavaScript

2️⃣ Application Layer

PHP (Core) OR Laravel Framework

MVC Architecture

Controllers

Routing

API Integration Logic

3️⃣ Database Layer

MySQL (Relational Database)

4️⃣ AI Layer

OpenAI API OR Template-Based Generator

5️⃣ Payment Engine

Demo payment simulation logic (custom-built)

6. UI/UX & Design Specifications
6.1 Design Philosophy

Minimal and premium interface

Large hero imagery

Glassmorphism search box

Card-based UI design

Soft shadows & rounded corners

Clear CTA hierarchy

Responsive grid layout

6.2 Landing Page Layout Structure
6.2.1 Header

Logo

Navigation (Home, Flights, Hotels, AI Planner, Contact)

Login / Register buttons

Sticky navbar on scroll.

6.2.2 Hero Section

Full-width background image with overlay.

Includes:

Headline

Subheading

Smart search widget (tabs: Flights | Hotels | Destination)

CTA Button

6.2.3 Search Widget

Fields:

Location

Dates

Guests

Budget

Filters

Search Button

Styled using:

Rounded container

Soft shadow

White card overlay

6.2.4 Travel Deals Section

Grid-based travel cards:

Image

Title

Description

Price

Book Now button

Hover animation enabled.

6.2.5 Popular Lodges Section

Horizontal scroll cards.

Each card includes:

Lodge Image

Name

Description

Price

Book Now

6.2.6 AI Itinerary Section

Dedicated page with:

Form (destination, days, budget, interests)

Generate button

Structured itinerary display

Save / Download option

6.2.7 FAQ Section

Accordion-based layout:

Expand/Collapse animation

6.2.8 Footer

Company info

Support links

Legal links

Newsletter input

Social icons

7. Functional Requirements
7.1 User Module

Users can:

Register

Login / Logout

Update profile

View booking history

Save itineraries

System must:

Hash passwords (bcrypt)

Validate inputs

Maintain secure sessions

Prevent SQL injection

7.2 Flight Search Module
User Inputs

From City

To City

Departure Date

Return Date

Passengers

Class

Output

Airline

Departure / Arrival Time

Duration

Price

Features

Sort by price

Sort by duration

Filter by airline

(Mock data stored in database)

7.3 Hotel Search Module
Inputs

City

Check-in / Check-out

Guests

Budget

Rating filter

Output

Hotel Name

Price per night

Rating

Amenities

Image

Features

Sort by rating

Sort by price

Filter by budget

7.4 Booking Module

Workflow:

User selects flight/hotel

System generates unique Booking ID

Displays booking summary

Redirects to payment page

Stores booking in database

7.5 Demo Payment Gateway Module
Purpose

Simulate real-world transaction system.

Payment Methods

Credit Card

Debit Card

UPI

Net Banking

Workflow

User enters payment details

Server validates format

2–3 second processing animation

Random outcome:

80% success

20% failure

Generate:

Transaction ID

Payment Status

Store in database

Display confirmation

Security Constraints

No real card data stored

Mask card numbers

Store only transaction ID & status

Prevent duplicate submission

CSRF protection

7.6 AI-Generated Itinerary Module (Core Feature)
Objective

Generate personalized day-wise itinerary based on user preferences.

User Inputs

Destination

Number of Days

Budget

Travel Type

Interests

System Output

Day-wise travel plan

Estimated daily cost

Budget breakdown

Suggested hotel

Must-visit attractions

Transport recommendations

Food suggestions

Travel tips

AI Processing Flow

Validate input

Construct structured prompt

Send to AI API

Receive formatted output

Display in UI

Optional save to database

Example Prompt Structure

Generate a detailed travel itinerary.

Destination: Goa
Number of Days: 3
Total Budget: ₹20000
Travel Type: Friends
Interests: Beaches, Adventure, Food

Requirements:

Day-wise plan

Budget breakdown

Suggested hotels

Local transport

Must-visit places

Travel tips

8. Database Design
users

id

name

email

password

created_at

flights

id

airline

from_city

to_city

departure_time

arrival_time

duration

price

class

hotels

id

name

city

price_per_night

rating

amenities

image

bookings

id

user_id

type (flight/hotel)

reference_id

total_price

booking_date

payments

id

booking_id

user_id

amount

payment_method

transaction_id

payment_status

payment_date

ai_itineraries

id

user_id

destination

days

budget

interests

travel_type

generated_plan (TEXT)

created_at

9. Non-Functional Requirements

Fully responsive design

Page load time < 3 seconds

Secure authentication

Modular code

Error handling

Scalable architecture

Clean UI/UX

Input sanitization

Session management

10. User Flow
Flight Booking Flow

Search → Select → Confirm → Payment → Success/Failure → Confirmation

Hotel Booking Flow

Search → Select → Confirm → Payment → Success/Failure → Confirmation

AI Planner Flow

Enter Preferences → Generate → View Itinerary → Save/Download

11. Admin Module

Admin can:

View users

Manage flights (CRUD)

Manage hotels (CRUD)

View bookings

View payments

View AI itineraries

Admin authentication required.

12. Security Considerations

Password hashing

CSRF tokens

Server-side validation

Prepared SQL statements

Session timeout

Payment duplication prevention

13. Testing Strategy

Unit Testing (modules)

Integration Testing

Payment simulation testing

AI response validation

UI responsiveness testing

